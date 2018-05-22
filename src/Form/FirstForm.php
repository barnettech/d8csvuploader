<?php
 
/**
 * @file
 * Contains \Drupal\hello_world\Form\FirstForm.
 */
 
namespace Drupal\hello_world\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
 
class FirstForm extends FormBase {
 
  /**
   *  {@inheritdoc}
   */
  public function getFormId() {
    return 'first_form';
  }
 
  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Use the Form API to define form elements.
    $form['my_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => t('Upload a CSV File to batch add users to the system'),
      '#size' => 20,
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/',
      '#upload_validators' => array(
        'file_validate_extensions' => array('csv'),
        'file_validate_size' => array(MAX_FILE_SIZE*1024*1024),
       ),
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Search'),
    );
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate the form values.
    $validators = array('file_validate_extensions' => array('csv'));

  // Check for a new uploaded file.
  $file = file_save_upload('csv_upload', $validators);
  //$file = $form_state['values']['csv_upload'];

  if (isset($file)) {
    // File upload was attempted.
    if ($file) {
      // Put the temporary file in form_values so we can save it on submit.
      $form_state['values']['csv_upload_file'] = $file;
    }
    else {
      // File upload failed.
      form_set_error('csv_upload', t('The file could not be uploaded.'));
    }
  }
     }
 
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Do something useful.
    drupal_set_message('thanks for submitting the form!');
    // dpm($form_state);
    $fid = $form_state->getValue('my_file');
    //dpm($fid);
    // dpm(File::load($fid[0]));
    // https://drupal.stackexchange.com/questions/19894/how-can-i-import-the-contents-of-an-uploaded-csv-file-into-a-drupal-managed-tabl
    if (!empty($fid)) {
      $file = File::load($fid[0]);
      $file->setPermanent();
      $file->save();
    }
   $data = file_get_contents($file->getFileUri());
      drupal_set_message($data); 
    
         
  }
}
