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

use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * Static content controller
 * This controller will render views from templates/Pages/
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class CronsController extends AppController
{

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        // methods name we can pass here which we want to allow without login
        parent::beforeFilter($event);
        /* https://book.cakephp.org/4/en/controllers/components/authentication.html#AuthComponent::allow */
        $this->Auth->allow();
        $this->autoRender = false;
    }



    public function index()
    {
    }

    public function sendEmail()
    {

        TransportFactory::setConfig('Manual', [
            'className' => 'Smtp', 
            //'className' => 'Debug',
            'tls' => true,
            'port' => 587, 'host' => 'mail.roifelawgroup.com',
            'username' => 'no_reply@roifelawgroup.com',
            'password' => '0B}Dz.1O]guL'
        ]);
        $mailer = new Mailer('default');
        $mailer->setTransport('Manual');

        $query = $this->Files->find('all')->where(['Files.is_notified' => 1])->limit(10);
        $data = $query->all()->toArray();
        if (!empty($data)) {
            foreach ($data as $list) {
                try {
                    $url = SITEURL . 'cdn/files/' . $list->file_name;
                    $msg = 'Hello, <br> New file uploaded, please download file from here ' . $url . ' ! <br>This file will be deleted after 24hrs';
                    $res = $mailer
                        ->setEmailFormat('both')
                        ->setFrom(['no_reply@roifelawgroup.com' => 'No Reply'])
                        ->setTo('saroya.com@gmail.com')
                        ->setSubject('New File uploaded - ' . DATE)
                        ->deliver($msg);

                        pr($res);die;


                    $up_arr = ['id' => $list->id, 'is_notified' => 2];
                    $saveData = $this->Files->newEntity($up_arr, ['validate' => false]);
                    $this->Files->save($saveData);
                    pr('<div style="color: green;">Email has been sent</div>');
                } catch (\Throwable $th) {

                    $up_arr = ['id' => $list->id, 'is_notified' => 3];
                    $saveData = $this->Files->newEntity($up_arr, ['validate' => false]);
                    $this->Files->save($saveData);
                    pr('<div style="color: red;"><b>Email has been failed</b></div>');
                }
            }
        }

        exit;
    }


    /* Setp 3 // lottery_noti
    send email to those selected for lottery 
    */
    public function remove()
    {
        $date = date('Y-m-d H:i:s', strtotime('-24 hours', strtotime(DATE)));
        $data = $this->Files->find()->where(['Files.is_notified' => 2, 'Files.created <=' => $date])->all();
        if (!$data->isEmpty()) {
            foreach ($data as $list) {
                $full_path = 'cdn/files/' . $list->file_name;
                if (file_exists($full_path)) {
                    $file = new File($full_path);
                    $file->delete();
                    pr("File was exists and deleted");
                } else {
                    pr("File not exists");
                }

                $this->Files->delete($list);

            }
        } else {
            pr('empty');
        }
        exit;
    }


    public function allFiles()
    {
        
        $data = $this->Files->find()->all();
        if (!$data->isEmpty()) {
            foreach ($data as $list) {
                $full_path = SITEURL.'cdn/files/' . $list->file_name;
                pr('<a href="'.$full_path.'" target="_blank">'.$list->file_name.'</a>');
            }
        }
        exit;
    }
}
