<?php

namespace Drupal\asu_quiz\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;
use Drupal\webform\Entity\WebformSubmission;


/**
 * Form submission handler
 *
 * @WebformHandler(
 *   id = "quiz_webform_handler",
 *   label = @Translation("Submit to SF"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Send the submission to SF"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 **/

class quizWebformHandler extends WebformHandlerBase {

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [];
    }

    /**
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

        /** @var Node $node */
       $values =  $webform_submission->getData();
       $websid = $values['sid'];
       // \Drupal::logger('webform-sid')->notice('<pre><code>' . print_r($values, TRUE) . '</code></pre>');


        $persona_agree = isset($values['do_the_above_results_sound_like_you_'])?$values['do_the_above_results_sound_like_you_']:'';
        $first_name_from_url = isset($values['first_name_from_url'])?$values['first_name_from_url']:'';
        $request_info_sid =  isset($values['rfi_sid'])?$values['rfi_sid']:'';
        $persona_original = isset($_SESSION['persona'])?$_SESSION['persona']:'';
        if($persona_original == "deep_diver"){
            $persona = 99;
        }
        if($persona_original == "trailblazer"){
            $persona = 100;
        }
        if($persona_original == "focused_futurist"){
            $persona = 101;
        }
        if($persona_original == "natural_networker"){
            $persona = 98;
        }
        if($persona_original == "superfan"){
            $persona = 97;
        }

        if($persona_agree != "No"){
            $first_name = isset($values['first_name'])?$values['first_name']:'';
            $last_name = isset($values['last_name'])?$values['last_name']:'';
            $email_address = isset($values['email_address'])?$values['email_address']:'';
            $zip_code = isset($values['zip_code'])?$values['zip_code']:'';
            $student_type = isset($values['student_type'])?$values['student_type']:'';
            $start_term = isset($values['start_term'])?$values['start_term']:'';
            $country = isset($values['country'])?$values['country']:'';


            $host = $_SERVER['HTTP_HOST'];

            if(isset($_SESSION["quiz_email_variables"])){
                unset($_SESSION["quiz_email_variables"]);
            }
            $http = 'https://';
            $url = $http.$host."/quiz";
            if(($host == "live-asu-admissions.ws.asu.edu") || ($host == "admission.asu.edu")){
                $posting_url = 'https://webapp4.asu.edu/formmanager/FormUserController?selection=1';
                $grad_url =  'https://requestinfo.asu.edu/prospect_form_post';
            }
            else{
                $posting_url = 'https://webapp4-qa.asu.edu/formmanager/FormUserController?selection=1';
                $grad_url =  'https://requestinfo-qa.asu.edu/prospect_form_post';
            }
            ///undergrad submissions
            if(empty($request_info_sid)){
                if($student_type != 'grad'){
                    $degree_level = 'ugrad';

                    $posting_data = array (
                        'form_id'=> 19,
                        'source_id'=> 99,
                        'field1'=> $first_name,
                        'field3'=> $last_name,
                        'field9'=> $zip_code,
                        'field11'=> $email_address,
                        'field13' => $country,
                        'field61'=> $student_type,
                        'field74'=> $start_term,
                        'field102'=> $url,
                        'field128' =>  $persona
                    );

                    //if submitting data from dev or test site, submit to testing environment

                    $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
                    // post the data
                    $at = http_build_query($posting_data, '', '&');
                    $options = array(
                        'method' => 'POST',
                        'data' => $at,
                        'timeout' => 15,
                        'headers' => $headers,
                    );
                    //dpm($at);
                    $client = \Drupal::httpClient();
                    $response = $client->request('POST', $posting_url, $options);
                    $code = $response->getStatusCode();
                    if ($code == 200) {
                        $body = $response->getBody()->getContents();
                     }
                  //  $response = drupal_http_request($posting_url, $options); // send the response
                    //dpm($response);
                }

                ///Grad submissions
                if($student_type == 'grad'){
                    $degree_level = 'grad';
                    $source_id = '';
                    $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
                    // build data array to post
                    $submission_data = array (
                        'source'=> '',
                        'firstName'=> $first_name ,
                        'lastName'=> $last_name,
                        'emailAddress'=> $email_address,
                        'projectedEnrollment'=>$start_term,
                        'countryOfCitizenship' => $country,
                        'zip' => $zip_code,

                    );

                    //url to post data to requestinfo
                    $curl = curl_init($grad_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //If you don't want to use any of the return information, set to false
                    curl_setopt($curl, CURLOPT_HEADER, FALSE); //Set this to false to remove informational headers
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $submission_data); //data mapping
                    curl_setopt($curl, CURLOPT_SSLVERSION, 1); //This will set the security protocol to TLSv1
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($curl);

                    $info = curl_getinfo($curl);

                    curl_close($curl);
                }
            }

        }


    }
}