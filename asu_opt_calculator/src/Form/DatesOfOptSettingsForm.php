<?php
	
	/**
	 *@file
	 *contains \Drupal\asu_opt_calculator\Form\DatesOfOptSettingsForm
	 **/
	
	namespace Drupal\asu_opt_calculator\Form;
	
	use Drupal\Core\Form\ConfigFormBase;
	use Drupal\Core\Form\FormStateInterface;
	
	
	/**
	 *Defines a form to configure Persoan Quiz confirmation page content settings
	 */
	
	class DatesOfOptSettingsForm extends ConfigFormBase{
		/**
		 *{ @inheritdoc}
		 */
		public function getFormID(){
			return 'asu_opt_calculator_calc_admin_settings';
		}
		
		/*
		 **{@inheritdoc}
		 */
		protected function getEditableConfigNames(){
			return [
				'calculator.calc_admin_settings'
			];
		}
		
		/*
		 **{@inheritdoc}
		 */
		public function buildForm(array $form, FormStateInterface $form_state) {
			$config = $this->config('calculator.admin_settings');
			
			$year = date("Y");
			$year_values =  array();
			$last_year = $year+6;
			
			for($i= $year; $i <= $last_year; $i++){
				$j = intval($i);
				$year_values[$j] = $j;
			}
			foreach(array_values($year_values) as $k => $yeardates) {
				
				$form["springdate$yeardates"] = array(
					'#type' => 'date',
					'#date_date_format' => 'm/d/Y',
					'#datepicker_options' => array(
						'minDate' => 0,
					),
					'#date_year_range' => "+$k:+7",
					'#date_label_position' => 'within',
					'#title' => t("Spring commencement date for $yeardates"),
					'#default_value' => $config->get("springdate" . "$yeardates"),
				
				);
				
				$form["springsessiondate$yeardates"] = array(
					'#type' => 'date',
					'#date_date_format' => 'm/d/Y',
					'#datepicker_options' => array(
						'minDate' => 0,
					),
					'#date_year_range' => "+$k:+7",
					'#date_label_position' => 'within',
					'#title' => t("Spring Session A date for $yeardates"),
					'#default_value' => $config->get("springsessiondate" . "$yeardates"),
				
				);
				
				
				$form["summerdate$yeardates"] = array(
					'#type' => 'date',
					'#date_date_format' => 'm/d/Y',
					'#datepicker_options' => array(
						'minDate' => 0,
					),
					'#date_year_range' => "+$k:+7",
					'#date_label_position' => 'within',
					'#title' => t("Summer commencement date for $yeardates"),
					'#default_value' => $config->get("summerdate" . "$yeardates"),
				);
				
				$form["summersessiondate$yeardates"] = array(
					'#type' => 'date',
					'#date_date_format' => 'm/d/Y',
					'#datepicker_options' => array(
						'minDate' => 0,
					),
					'#date_year_range' => "+$k:+7",
					'#date_label_position' => 'within',
					'#title' => t("Summer Session A date for $yeardates"),
					'#default_value' => $config->get("summersessiondate" . "$yeardates"),
				);
				
				$form["falldate$yeardates"] = array(
					'#type' => 'date',
					'#date_date_format' => 'm/d/Y',
					'#datepicker_options' => array(
						'minDate' => 0,
					),
					'#date_year_range' => "+$k:+7",
					'#date_label_position' => 'within',
					'#title' => t("Fall commencement date for $yeardates"),
					'#default_value' => $config->get("falldate" . "$yeardates"),
				);
				
				$form["fallsessiondate$yeardates"] = array(
					'#type' => 'date',
					'#date_date_format' => 'm/d/Y',
					'#datepicker_options' => array(
						'minDate' => 0,
					),
					'#date_year_range' => "+$k:+7",
					'#date_label_position' => 'within',
					'#title' => t("Fall Session A date for $yeardates"),
					'#default_value' => $config->get("fallsessiondate" . "$yeardates"),
				);
			}
				
			
			return parent::buildForm($form, $form_state);
		}
		
		/*
	   **{@inheritdoc}
	   */
		public function submitForm(array &$form, FormStateInterface $form_state)
		{
			parent::submitForm($form, $form_state);
			$year = date("Y");
			$year_values = array();
			$last_year = $year + 6;
			
			for ($i = $year; $i <= $last_year; $i++) {
				$j = intval($i);
				$year_values[$j] = $j;
			}
			
			foreach (array_values($year_values) as $k => $yeardates) {
				
				\Drupal::service('config.factory')->getEditable('calculator.admin_settings')->set("springdate$yeardates", $form_state->getValue("springdate$yeardates"))->save();
				\Drupal::service('config.factory')->getEditable('calculator.admin_settings')->set("springsessiondate$yeardates", $form_state->getValue("springsessiondate$yeardates"))->save();
				\Drupal::service('config.factory')->getEditable('calculator.admin_settings')->set("summerdate$yeardates", $form_state->getValue("summerdate$yeardates"))->save();
				\Drupal::service('config.factory')->getEditable('calculator.admin_settings')->set("summersessiondate$yeardates", $form_state->getValue("summersessiondate$yeardates"))->save();
				\Drupal::service('config.factory')->getEditable('calculator.admin_settings')->set("falldate$yeardates", $form_state->getValue("falldate$yeardates"))->save();
				\Drupal::service('config.factory')->getEditable('calculator.admin_settings')->set("fallsessiondate$yeardates", $form_state->getValue("fallsessiondate$yeardates"))->save();
				
			}
			
		}
		
	}