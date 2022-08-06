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

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

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

    public function _refreshToken()
    {
        include('dropbox/vendor/autoload.php');
        $app = new DropboxApp("whteik9yipt06p1", "ign4n804lg19se4", dropbox_token);
        $dropbox = new Dropbox($app);
        $authHelper = $dropbox->getAuthHelper();
        $accessToken = $authHelper->getRefreshedAccessToken(dropbox_token);
        pr($accessToken);
    }

    public function dropboxCallback()
    {


        include('dropbox/vendor/autoload.php');
        $app = new DropboxApp("whteik9yipt06p1", "ign4n804lg19se4", dropbox_token);
        $dropbox = new Dropbox($app);
        $authHelper = $dropbox->getAuthHelper();
        $callbackUrl = SITEURL . "crons/dropbox_callback";

        if (isset($_GET['code']) && isset($_GET['state'])) {
            $code = $_GET['code'];
            $state = $_GET['state'];
            //Fetch the AccessToken
            $accessToken = $authHelper->getAccessToken($code, $state, $callbackUrl);
            echo $accessToken->getToken();
        }
        die;
    }
    public function dropboxLogin()
    {
        include('dropbox/vendor/autoload.php');
        $app = new DropboxApp("whteik9yipt06p1", "ign4n804lg19se4", dropbox_token );
        $dropbox = new Dropbox($app);
        $authHelper = $dropbox->getAuthHelper();
        $callbackUrl = SITEURL."crons/dropbox_callback";
        $authUrl = $authHelper->getAuthUrl($callbackUrl);
        pr($authUrl);die;
    }

    public function dropbox()
    {
        
        include('dropbox/vendor/autoload.php');
        $app = new DropboxApp("whteik9yipt06p1", "ign4n804lg19se4", dropbox_token );
        $dropbox = new Dropbox($app);

        /*
        $authHelper = $dropbox->getAuthHelper();
        $callbackUrl = SITEURL."crons/dropbox_callback";
        $authUrl = $authHelper->getAuthUrl($callbackUrl);
        pr($authUrl);die;
        */

        /*
        
        $fileMetadata = $dropbox->getMetadata("/FTP_upload/yogesh-saroya");
        $a = $fileMetadata->getName();
        pr($fileMetadata);
        $a = $dropbox->postToAPI('/files/permanently_delete', ['path' => "/FTP_upload"]);
        pr($a);die;
        */


        $data = $this->Files->find()->contain(['Clients'])->where(['Clients.is_syncro' => 1])->all();

        if (!$data->isEmpty()) {
            echo "<ul>";
            foreach ($data as $list) {
                $filePath = 'cdn/files/' . $list->client->folder . "/" . $list->file_name;
                $fileName = $list->client->folder . "/" . $list->file_name;
                try {
                    // Create Dropbox File from Path
                    $dropboxFile = new DropboxFile($filePath);

                    // Upload the file to Dropbox
                    $uploadedFile = $dropbox->upload($dropboxFile, "/FTP_upload/" . $fileName, ['autorename' => true]);

                    // File Uploaded
                    echo $uploadedFile->getPathDisplay();
                    echo "<br>";

                    $list->is_syncro = 2;
                    $this->Clients->save($list);
                } catch (DropboxClientException $e) {
                    pr($e->getMessage());
                    die;
                }
            }
        } else {
            echo "Empty";
        }
        exit;
    }

    public function testEmail()
    {

        $mailer = new Mailer('default');


        TransportFactory::setConfig('Manual', [
            'className' => 'Smtp', 'tls' => false, 'port' => 25, 'host' => 'localhost',
            'username' => 'info@roifelawgroup.info', 'password' => 'Q9}kE[cJ(vXQ'
        ]);

        /*
        TransportFactory::setConfig('Manual', [
            'className' => 'Smtp','tls' => true,'port' => 587,
            'host' => 'mail.superpad.finance',
            'username' => 'support@superpad.finance','password' => 'super@1234!'
        ]); */

        $mailer->setTransport('Manual');

        $res = $mailer->setEmailFormat('both')->setFrom(['info@roifelawgroup.info' => 'Info'])->setTo('yogeshsaroya@gmail.com')->setSubject('Test email  -' . DATE)->deliver('Test message from Developers Server - ' . rand(123, 987));
        $res2 = $mailer->setEmailFormat('both')->setFrom(['info@roifelawgroup.info' => 'Info'])->setTo('roifelawgroup@gmail.com')->setSubject('Test email  -' . DATE)->deliver('Test message from Developers Server - ' . rand(123, 987));
        $res3 = $mailer->setEmailFormat('both')->setFrom(['info@roifelawgroup.info' => 'Info'])->setTo('arthur.gallagher@roifelawgroup.com')->setSubject('Test email  -' . DATE)->deliver('Test message from Developers Server - ' . rand(123, 987));
        $res4 = $mailer->setEmailFormat('both')->setFrom(['info@roifelawgroup.info' => 'Info'])->setTo('staff@roifelawgroup.com')->setSubject('Test email  -' . DATE)->deliver('Test message from Developers Server - ' . rand(123, 987));


        pr($res);
        pr($res2);
        pr($res3);
        pr($res4);
        die;
    }

    public function sendEmail()
    {
        $data = $this->Clients->find()->contain(['Files'])->where(['Clients.is_notified' => 1])->limit(10)->all();
        if (!$data->isEmpty()) {

            TransportFactory::setConfig('Manual', [
                'className' => 'Smtp', 'tls' => false, 'port' => 25, 'host' => 'localhost',
                'username' => 'info@roifelawgroup.info', 'password' => 'Q9}kE[cJ(vXQ'
            ]);
            $mailer = new Mailer('default');
            $mailer->setTransport('Manual');

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

                        $mailer->setFrom(['info@roifelawgroup.info' => 'Upload'])->setEmailFormat('both')->setTo('roifelawgroup@gmail.com')
                            ->setSubject('Upload to Roife Law Group from: ' . $list->full_name)->deliver($msg);

                        $mailer->setFrom(['info@roifelawgroup.info' => 'Upload'])->setEmailFormat('both')->setTo('staff@roifelawgroup.com')
                            ->setSubject('Upload to Roife Law Group from: ' . $list->full_name)->deliver($msg);

                        $mailer->setFrom(['info@roifelawgroup.info' => 'Upload'])->setEmailFormat('both')->setTo($list->email)->setSubject('Files uploaded at Roife Law Group')->deliver($msg_user);


                        $list->is_notified = 2;
                        $this->Clients->save($list);
                        pr('<div style="color: green;">Email has been sent</div>');
                    } catch (\Throwable $th) {
                        pr($th);

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
