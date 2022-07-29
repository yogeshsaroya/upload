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


    public function testEmail(){

        $mailer = new Mailer('default');
        TransportFactory::setConfig('Manual', [
            'className' => 'Smtp',
            //'className' => 'Debug',
            'tls' => true,
            'port' => 587,
            'host' => 'info@roifelawgroup.info',
            'username' => 'a2plvcpnl424424.prod.iad2.secureserver.net',
            'password' => 'Q9}kE[cJ(vXQ'
        ]);
        $mailer->setTransport('Manual');


        $res = $mailer
            ->setEmailFormat('both')
            ->setFrom(['info@roifelawgroup.info' => 'Info'])
            ->setTo('yogeshsaroya@gmail.com')
            ->setSubject('New File uploaded -' . DATE)
            ->deliver('Test message from Developers Server - '.rand(123,987));
            pr($res);
        die;
    }

    public function sendEmail()
    {
        $data = $this->Clients->find()->contain(['Files'])->where(['Clients.is_notified' => 1])->limit(10)->all();
        if (!$data->isEmpty()) {
            foreach ($data as $list) {
                $ul = $li = null;
                if (!empty($list->files)) {
                    foreach ($list->files as $fl) {
                        $full_path = SITEURL . 'cdn/files/' . $list->folder . "/" . $fl->file_name;
                        $li .= '<li style="padding: 10px;list-style-type: disclosure-closed;"><a href="' . $full_path . '" target="_blank">' . $fl->file_name . '</a></li>';
                    }
                    $ul = "<ul>$li</ul>";
                    try {
                        $msg = "<html><head><title>Email</title></head><body><table><tr><td>Hello Admin</td></tr><tr><td><br></td></tr>
                        <tr><td>New files uploaded by : -</td></tr><tr><td></td></tr>
                        <tr><td>Full Name : $list->full_name</td></tr><tr><td>Email : $list->email</td></tr><tr><td>Mobile Number : $list->phone</td></tr><tr><td><br></td></tr>
                        <tr><td>List of uploaded files:</td></tr><tr><td>$ul</td></tr><tr><td></td></tr>
                        <tr><td>This file will be deleted after 8hrs. <br>Thanks </td></tr></table></body></html>";

                        $msg_user = "<html><head><title>Email</title></head><body><table><tr><td>Hello $list->full_name</td></tr><tr><td><br></td></tr>
                        <tr><td>This is the confirmation email of below files was uploaded at Roife Law Group</td></tr><tr><td>$ul</td></tr><tr><td></td></tr>
                        <tr><td><br>Thanks </td></tr></table></body></html>";



                        $mailer = new Mailer('default');
                        $res = $mailer->setFrom(['upload@roifelawgroup.com' => 'Upload'])->setEmailFormat('both')->setTo('admin@roifelawgroup.com')
                            ->setSubject('Upload to Roife Law Group from: ' . $list->full_name)->deliver($msg);
                        $mailer->reset();

                        $mailer2 = new Mailer('default');
                        $res = $mailer2->setFrom(['upload@roifelawgroup.com' => 'Upload'])->setEmailFormat('both')->setTo('staff@roifelawgroup.com')
                            ->setSubject('Upload to Roife Law Group from: ' . $list->full_name)->deliver($msg);
                        $mailer2->reset();


                        $mailer1 = new Mailer('default');
                        $res1 = $mailer1->setFrom(['upload@roifelawgroup.com' => 'Upload'])->setEmailFormat('both')->setTo($list->email)->setSubject('Files uploaded at Roife Law Group')->deliver($msg_user);
                        $mailer1->reset();

                        $list->is_notified = 2;
                        $this->Clients->save($list);
                        pr('<div style="color: green;">Email has been sent</div>');
                    } catch (\Throwable $th) {

                        $list->is_notified = 3;
                        $this->Clients->save($list);
                        pr('<div style="color: red;"><b>Email has been failed</b></div>');
                    }
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
        $date = date('Y-m-d H:i:s', strtotime('-8 hours', strtotime(DATE)));
        $data = $this->Clients->find()->contain(['Files'])->where(['Clients.is_notified' => 2, 'Clients.created <=' => $date])->all();
        if (!$data->isEmpty()) {
            foreach ($data as $list) {
                $full_path = 'cdn/files/' . $list->folder;
                if (file_exists($full_path)) {
                    $folder = new Folder($full_path);
                    if ($folder->delete()) {
                        pr("File was exists and deleted");
                    }
                } else {
                    pr("File not exists");
                }
                $this->Clients->delete($list);
                $this->Files->deleteMany($list['files']);
            }
        } else {
            pr('empty');
        }
        exit;
    }


    public function allFiles()
    {

        $data = $this->Files->find()->contain(['Clients'])->all();
        if (!$data->isEmpty()) {
            echo "<ul>";
            foreach ($data as $list) {
                $full_path = SITEURL . 'cdn/files/' . $list->client->folder . "/" . $list->file_name;
                echo '<li style="padding: 10px;list-style-type: disclosure-closed;}"><a href="' . $full_path . '" target="_blank">' . $list->file_name . '</a></li>';
            }
            echo "</ul>";
        } else {
            echo "Empty";
        }
        exit;
    }
}
