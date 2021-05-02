<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * FCM simple server side implementation in PHP
 *
 * @author Abhishek
 */
class Fcm
{

    /**
     * Function to send notification to a single device
     *
     * @param   string   $to     registration id of device (device token)
     * @param   array   $message    push notification array returned from getPush()
     *
     * @return  array   array of notification data and to address
     */
    public function send($to, $notification, $data=null)
    {
        $fields = array(
            'registration_ids' => $to,
            'collapse_key' => "type_a",
            'notification' => $notification,
            'data' => $data,
        );
        return $this->sendPushNotification($fields);
    }

    /**
     * Function to send notification to a topic by topic name
     *
     * @param   string   $to     topic
     * @param   array   $message    push notification array returned from getPush()
     *
     * @return  array   array of notification data and to address (topic)
     */

    /**
     * Function makes curl request to firebase servers
     *
     * @param   array   $fields    array of registration ids of devices (device tokens)
     *
     * @return  string   returns result from FCM server as json
     */
    private function sendPushNotification($fields)
    {

        $CI = &get_instance();
        $CI->load->config('androidfcm'); //loading of config file

        // Set POST variables
        $url = $CI->config->item('fcm_url');

        $headers = array(
            'Authorization: key='.$CI->config->item('key'),
            'Content-Type: application/json',
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === false)
        {
            die('Curl failed: ' . curl_error($ch));
            return 0;
        }

        // Close connection
        curl_close($ch);

        //return $result;
        return 1;
    }

}
