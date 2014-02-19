                            Part: III

(written by Urb LeJeune <urb@e-government.com>)
!!! Thanks a Lot !!!
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

Public Functions

  // Constructor

  Your POP3 session can be logged by passing two variables when you instantiate
  the pop3 class as in the following example.

  $PerformLogging = TRUE;
  $LogFileName = "pop3.class.log";

  $pop3 = new POP3($PerformLogging,$LogFileName);

  - Only the POP3 commands and server responses are save in the log file.
  - Error messages are displayed on your browser.

  APOP (Authenticated Post Office Protocol)
  Every mail server connection you make sends your username and password across
  the network in clear text. This is a popular way for hackers to see your
  password using a "sniffer" program. With APOP, your password is encrypted
  while being transmitted over the network.

  Before establishing a connection set:
  $apop_detect = TRUE or FALSE

  Now all mailservers support ASOP.

  // Functions
  The follow class methods are available.

  //////////
  connect($server, $port = "110", $timeout = "25", $sock_timeout = "10,500")

  // Vars:
    - $server ( Server IP or DNS )
    - $port ( Server port default is "110" )
    - $timeout ( Connection timeout for connect to server )
    - $sock_timeout ( Socket timeout for all actions   (10 sec 500 msec) = (10,500))

  If connection is established the method returns TRUE. If the connection is not
  successfully established FALSE is returned and $this->error = msg is displayed.

  //////////
  login($user,$pass,$apop = "0")
  // Vars:
    - $apop  ( 1 = true and 0 = false)  (default = 0)

  //////////
  get_office_status()
    - If an error Connection will closed.
    - A successful Connection will return an associated array such as the following:

Array
(
    [count_mails] => 3
    [octets] => 3257477
    [1] => Array
        (
            [size] => 832
            [uid] => 617999468
        )

    [2] => Array
        (
            [size] => 3253781
            [uid] => 617999616
        )

    [3] => Array
        (
            [size] => 2864
            [uid] => 617999782
        )
)

  /////////
  get_mail($msg_number)
    - If the command fails the connection is not closed and FALSE is returned.
    - If get_mail() succeeds, an array is returned where every line of the
    mail message, including the header, is an element of the array. Such as:

  Array(
     [0] => "line1"
     [1] => "line2"
     ....
     )

//////////////////////////////////////////////////////////////////////////
//  IMPORTANT                                                           //
// If your mail count is high or your connection time is slow, or both, //
// you may exceed the default execution time of 30 seconds. In that     //
// case set the execution time to more than 30 seconds.                 //
//////////////////////////////////////////////////////////////////////////

  /////////
  delete_mail($msg_number)
    - Mark an email as delete
    - You must executed the close() method for the mail to be deleted.
    - If program terminates without executing a close() method the
      command, connection will not closed.
    - Execute reset() to unmark messages previously marked as deleted.
    - If the command fails the connection is not closed and FALSE is returned.

  /////////
  save2file($message,$filename)
    - $message must be a numeric array with each line terminated with a "CRLF".
    - If the command fails the connection is not closed and FALSE is returned.
  Array(
     [0] => "line1"
     [1] => "line2"
     ....
     )

   $filename
     - The default file name is  base64_encode(uid).".txt"
     - To check if you have download this mail
       base64_encode(uid).".txt" == $filename

   // Example directories and file name
   win32  .//mails// or c://ownfiles//etc...
   linux  ./mails/   or /dev/hda1/ownfiles/etc...

  /////////
  save2mysql($message,$mysql_socket,$dir_table = "inbox", $msg_table = "messages",$read = "0")
  // If the command fails the connection is not closed and FALSE is returned.
  // If mail already in $msg_table exists the method return false.
  // If there is a mysql error the method returns false and
  // mysql_errno() ." -- ". mysql error is set on $pop3-error
  // like this: "1054 -- Unknown column '' in 'field list'"
  //
  // The method checks toe establish if mail exists or not.
  // When mail exists the method returns false and an errormsg is sent on $pop3->error
  //
  // !!!!!!!!!!!!!!!!!!!!!!! IMPORTANT !!!!!!!!!!!!!!!!!!!!!!!!!!
  // In $msg_table give a field named "unique_id", the value
  // is a md5 fingerprint from $dir_table field "msg_id"
  //
  // If the "received" field in $dir_table spans more than one Server the Received
  // Strings are split with a "<next>"
  // As an example: $dir_table["received"] =
  //   Received: from ns10493a.cobalthosting.net by mx02.web.de with smtp
  //   <next> Received: (from httpd@localhost)
  // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

  /////////
  noop()
  - Send the "NOOP" command to server
  - If the command fails, the connection will not closed !!
  /////////
  get_top($msg_number,$lines="0")
    - If you only want header information use get_top().
    - The format is get_top($msg_number,"number-of-lines")
    - The "number-of-lines" parameter determines the number of lines to be read.
    - If "number-of-lines" exceeds the line count, the entire mail message is returned.
    - If the command fails the connection is not closed and FALSE is returned.

  /////////
  reset()
    - All mail previously marked as deleted will be marked as undeleted.
    - If the command fails the connection is not closed and FALSE is returned.

  /////////
  uidl($msg_number) (default = "0")
  - If $msg_number is set to FALSE (0 or null) the entire uid list is returned.
  - If $msg_number is set to a valid message number, the uid for the designed
    mail is returned..
  - If the command fails the connection is not closed and FALSE is returned.

////////////////////////////////////////////////////////////////////////////////

Private Functions

  /////////
  _putline($string)
    - Put a command to server socket.
    - $string should not be terminated with "CRLF".

  /////////
  _getnextstring()
    - optional: $buffer_size (default = "512")
    - get the next String from server socket.

  /////////
  _logging($string)
    - $string is written to the log_file as established in the class constructor.
    - $string should not be terminated with "CRLF".

  /////////
  _checkstate($string)
    - Check the pop3 server connection state.
    - $string is set to method (function) function name.

  /////////
  _parse_banner($server_text)
    - $server_text = first response after connect.
    - Returns the server banner for APOP Login command.

  /////////
  _cleanup()
    - unset some vars
    - close log_file
    - close server socket

  /////////
  _stats()
    - Get maildrop stats
    - If successful return an associative array containing:
      ["count_mails"]
      ["octets"]

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////



--------------------------------------------------------------------------------
----------------------------------END-------------------------------------------
--------------------------------------------------------------------------------

