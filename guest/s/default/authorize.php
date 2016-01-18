<?php
/* Orignally written by Andrew Niemantsverdriet 
 * email: andrewniemants@gmail.com
 * website: http://www.rimrockhosting.com
 *
 * This code is on github: https://github.com/kaptk2/portal
 *
 * Copyright (c) 2012, Andrew Niemantsverdriet
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met: 
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer. 
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution. 
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are those
 * of the authors and should not be interpreted as representing official policies, 
 * either expressed or implied, of the FreeBSD Project.
 */

// Start the session to get access to the saved variables
session_start();



function sendAuthorization($id, $minutes) {
    
    // Unifi Connection details
    
    $unifyServer = "https://66.29.173.250:8443";
    $unifyUser = "admin";
    $unifyPass =  "P@ss4sundance";
    
   echo "Initialzed CURL <br>";
  // Start Curl for login
  $ch = curl_init();

  // We are posting data
  curl_setopt($ch, CURLOPT_POST, TRUE);
  // Set up cookies
  $cookie_file = "/tmp/unifi_cookie";
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
  // Allow Self Signed Certs
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_SSLVERSION, 1);
  // Login to the UniFi controller
  curl_setopt($ch, CURLOPT_URL, $unifyServer. "/api/login");
  echo "Logging into unifi controller at url: " . $unifyServer."/api/login <br>";
      
  $data = json_encode(array('username' => $unifyUser,'password' => $unifyPass));

  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  $content = curl_exec ($ch);
  if (FALSE === $content)
  {
      echo "<br> <span style=\"color:red;\">" . curl_error($ch). curl_errno($ch) . "</span>";
  }
  echo "<br> CURL COMMAND EXECUTED";
  // Send user to authorize and the time allowed
  $data = json_encode(array(
          'cmd'=>'authorize-guest',
          'mac'=>$id,
          'minutes'=>$minutes));

  // Make the API Call
  echo "<br> MAKING API CALL";
  curl_setopt($ch, CURLOPT_URL, $unifyServer.'/api/s/default/cmd/stamgr');
  curl_setopt($ch, CURLOPT_POSTFIELDS, 'json='.$data);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  $content = curl_exec ($ch);
      if (FALSE === $content)
      {
          echo "<br> <span style=\"color:red\"" . curl_error($ch). curl_errno($ch) ."</span>";
      }
  echo "<br> CURL COMMAND EXECUTED";
  
  // Logout of the connection
  echo "<br> CLOSING UNIFI CONNECTION";
  curl_setopt($ch, CURLOPT_URL, $unifyServer."/logout");
  
  $content = curl_exec ($ch);
      if (FALSE === $content)
      {
          echo "<br> <span style=\"color:red\"" . curl_error($ch). curl_errno($ch) ."</span>";
      }
         
  
      
  curl_close ($ch);
  echo "<br> CURL COMMAND EXECUTED";
  
  sleep(10); // Small sleep to allow controller time to authorize
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    sendAuthorization($_SESSION['id'], (72 *60));
}


?>
<!DOCTYPE html><html lang="en">
<head>

<meta charset="UTF-8">

</head>
<body>
</body>
</html>