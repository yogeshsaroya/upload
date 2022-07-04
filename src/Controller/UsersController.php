<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;


use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\Auth\DefaultPasswordHasher;
use PhpParser\Node\Stmt\TryCatch;
use Cake\Utility\Text;


/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class UsersController extends AppController
{

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        // methods name we can pass here which we want to allow without login
        parent::beforeFilter($event);
        /* https://book.cakephp.org/4/en/controllers/components/authentication.html#AuthComponent::allow */
        $this->Auth->allow(['login']);
    }


    public function index()
    {
        $this->redirect(SITEURL);
    }

    public function _up_files($data){
        $uploadPath = 'cdn/files/';
        if (!file_exists($uploadPath)) {
              mkdir($uploadPath, 0777, true);
        }
        $chk = 0;
        if(!empty($data)){
            foreach($data as $fileobject){
                $file_name = null;
                $saveData = null;
                $file_1 = $fileobject->getClientFilename();
                $name = pathinfo($file_1, PATHINFO_FILENAME);
                $ext = pathinfo($file_1, PATHINFO_EXTENSION);
                $file_name = Text::slug(rand(11111,99999)."-".$name).".".$ext;
                
                $destination = $uploadPath . $file_name;
                try {
                    $fileobject->moveTo($destination);
                    $getTbl = $this->Files->newEmptyEntity();
                    $saveData['file_name'] = $file_name;
                    $chkTbl = $this->Files->patchEntity($getTbl, $saveData, ['validate' => false]);
                    if ($this->Files->save($chkTbl)) { $chk++; } 
                } catch (Exception $e) { }    
            }
        }
        return $chk;
        
    }

    public function upload()
    {

        if ($this->request->is('ajax')) {
            if (!empty($this->request->getData())) {
                $a1 = $a2 = $a3 = $a4 = $a5 = 0;
                $postData = $this->request->getData();
                try {
                    if (!empty($postData['files_1'][0])) { $a1 = $this->_up_files($postData['files_1']); }
                    if (!empty($postData['files_2'][0])) { $a2 = $this->_up_files($postData['files_2']); }
                    if (!empty($postData['files_3'][0])) { $a3 = $this->_up_files($postData['files_3']); }
                    if (!empty($postData['files_4'][0])) { $a4 = $this->_up_files($postData['files_4']); }
                    if (!empty($postData['files_5'][0])) { $a5 = $this->_up_files($postData['files_5']); }
                    $tot = $a1 + $a2 + $a3 + $a4 + $a5;
                        if($tot > 0 ){
                            echo "<script>$('.rm_div').html('');</script>";
                            echo "<div class='alert alert-success'>Totla $tot Files has been uploaded.</div>";
                        }else{
                            echo '<div class="alert alert-danger" role="alert">Image not uploaded. Please select files.</div>'; exit;
                        }
                        
                    
                } catch (\Throwable $th) {
                    echo '<div class="alert alert-danger">Please try again.</div>';
                }
            }

            exit;
        }
    }

    /**
     * REF : https://book.cakephp.org/4/en/controllers/components/authentication.html#manually-logging-users-in
     */
    public function login()
    {

        /*
        Username: client 
        Password: roifelawgroup
        */
        $session = $this->getRequest()->getSession();
        $q = $this->request->getQuery();

        if ($this->Auth->User('user_name') != "") {
            if ($this->request->is('ajax')) {
                $u = SITEURL;
                echo "<script>window.location.href ='" . $u . "'; </script>";
                exit;
            } else {
                $this->redirect(SITEURL);
            }
        }

        if ($this->request->is('ajax') && !empty($this->request->getData())) {
            $post_data = $this->request->getData();
            if (empty($post_data['user_name'])) {

                echo '<div class="alert alert-danger">Please enter user name.</div>';
            } elseif (empty($post_data['password'])) {
                echo '<div class="alert alert-danger">Please enter password.</div>';
            } elseif ($post_data['user_name'] != 'client' || $post_data['password'] != 'roifelawgroup') {
                echo '<div class="alert alert-danger">Username or password is incorrect.</div>';
            } else {
                $this->Auth->setUser($post_data);
                $q_url = SITEURL;
                echo '<script>window.location.href = "' . $q_url . '"</script>';
                exit;
            }


            exit;
        }
    }
}
