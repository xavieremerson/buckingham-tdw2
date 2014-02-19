<?php
// vi: expandtab sw=4 ts=4 sts=4 nowrap nu fdm=marker:
/**
 * POP3 Class
 * 
 * This class provide access to a pop3 server through the pop3 protocol
 *  
 * @need: >=php-5.2.x
 * @author: j0inty.sL
 * @email: bestmischmaker@web.de
 * @version: 0.5.6-dev
 *
 * NOTES:
 * - !!! THIS IS A DEVELOPMENT VERSION !!!
 * - IPv6 support NEVER tested
 * - 
 * TODO:
 * - POP3_Result (or name POP3_Message ) ???
 * - clear the list function problem !!!
 * - 
 */
// {{{ final class POP3_Exception
final class POP3_Exception extends Exception
{
    // {{{ __construct()
	function __construct( $strErrMessage, $intErrCode )
	{
        switch( $intErrCode )
        {
            case POP3::ERR_NOT_IMPLEMENTS:
                $strErrMessage = "This function isn't implements at time.";
            break;
            
            case POP3::ERR_SOCKETS:
               $strErrMessage = "Sockets Error: (". socket_last_error() .") -- ". socket_strerror(socket_last_error());
            break;
            case POP3::ERR_INVALID_STATE:
                $strErrMessage = "Invalid State for function (". $strFunc .") !!! Please check your Code !!!";
            break;
        }
	    parent::__construct($strErrMessage, $intErrCode);	
	}
	// }}}
    // {{{ __toString()
	public function __toString()
	{
		return __CLASS__ ." [". $this->getCode() ."] -- ". $this->getMessage() ." in file ". $this->getFile() ." at line ". $this->getLine(). PHP_EOL ."Trace: ". $this->getTraceAsString() .PHP_EOL;
	}
    // }}}
}
// }}}
// {{{ class POP3
class POP3
{ 
    // {{{ Error constants
	const ERR_NONE = 0;
	const ERR_LOG = 1;
	const ERR_SOCKETS = 2;
	const ERR_PARAMETER = 3;
    const ERR_NOT_IMPLEMENTS = 4;
    const ERR_INVALID_STATE = 5;
    const ERR_STREAM = 6;
	const ERR_SEND_CMD = 7;
	// }}}
    // {{{ POP3 state constants
    const STATE_DISCONNECT = 100;
    const STATE_AUTHORIZATION = 101;
    const STATE_TRANSACTION = 102;
    // }}}
    // {{{ private attributes
    const DEFAULT_BUFFER_SIZE = 4096;
	private $bLogOpened = FALSE;
	private $resLogFp = FALSE;
	private $strLogFile = NULL;
    private $bHideUsernameAtLog = TRUE;

    private $bUseSockets;
	private $bSocketConnected = FALSE;
    private $strHostname = NULL;
    private $strIPAdress = NULL;
    private $intPort = NULL;

    private $intCurState = self::STATE_DISCONNECT;
    private $strAPOPBanner = NULL;
	private $bAPOPAutoDetect;
    // }}}
    // {{{ __construct()
    /*
     * Constructor
     *
     * @param bool $bUseSockets  Use the socket extension (uncomment php_sockets.dll or php_sockets.so in your php.ini file)
     * @param NULL|string $strLogFile  Path to a log file or NULL for no log
     * @param bool $bAPOPAutoDetect  APOP Auto Dection of|off
     * @param bool $bHideUsernameAtLog
     *
     * @throw POP3_Exception
     */
	public function __construct( $bUseSockets = TRUE, $strLogFile = NULL, $bAPOPAutoDetect = TRUE, $bHideUsernameAtLog = TRUE )
    {
        // Check for sockets extension if needed
		if( !is_bool($bUseSockets) )
		{
			throw new POP3_Exception("Invalid use sockets extension parameter given.", self::ERR_PARAMETER);
		}
        elseif( $bUseSockets && extension_loaded("sockets") )
		{
			$this->bUseSockets = TRUE;
		}
		else
		{
			$this->bUseSockets = FALSE;
		}

        if( !is_bool($bAPOPAutoDetect) )
		{
			throw new POP3_Exception("Invalid APOP auto detect parameter given.", self::ERR_PARAMETER);
		}
		if( !is_bool($bHideUsernameAtLog) )
        {
            throw new POP3_Exception("Invalid Hide Username at log file parameter given.", self::ERR_PARAMETER);
        }
		// Activate logging if needed
		if( !is_null($strLogFile) )
		{
			$this->strLogFile = $strLogFile;
			$this->openlog();
		}
		// Activate or Deactivate APOP Auto Detect mechanism
		$this->bAPOPAutoDetect = $bAPOPAutoDetect;
        $this->bHideUsernameAtLog = $bHideUsernameAtLog;
	}
    // }}}
    // {{{ __destruct()
	/*
     * Destructor
     *
     * @throw POP3_Exception
     */
	public function __destruct()
	{
		$this->disconnect();
		$this->closelog();
	}
    // }}}
    // {{{ connect()
    /*
     * connect
     *
     * @param NULL|string $strHostname  Hostname or ip adress of a pop3 server
     * @param integer $intPort  The port for the pop3 service (default is 110)
     * @param array $arrConnectionTimeout  array("sec" => "", "usec" => "")
     * @param bool $bIPv6  IP Version 6 Protocol
     *
     * @throw POP3_Exception
     */
	public function connect( $strHostname = NULL, $intPort = 110, $arrConnectionTimeout = array("sec" => 10, "usec" => 0), $bIPv6 = FALSE )
	{
	    if( !$this->checkState(POP3::STATE_DISCONNECT) )
        {
            throw new POP3_Exception("",self::ERR_INVALID_STATE);
        }
        /// Parameter checks ///
        if( !is_string($strHostname) )
        {
            throw new POP3_Exception("Invalid host parameter given", self::ERR_PARAMETER);
        }

        if( !is_int($intPort) || $intPort < 1 || $intPort > 65535 ) 
        {
            throw new POP3_Exception("Invalid port parameter given", self::ERR_PARAMETER);
        }
        if( !is_array($arrConnectionTimeout) || !is_int($arrConnectionTimeout["sec"]) || !is_int($arrConnectionTimeout["usec"]) )
        {
            throw new POP3_Exception("Invalid connection timeout parameter given", self::ERR_PARAMETER);
        }
        if( !is_bool($bIPv6) )
        {
            throw new POP3_Exception("Invalid IPv6 parameter given", self::ERR_PARAMETER);
        }
        
        $this->strHostname = $strHostname;
        $this->intPort = $intPort; 
        
        /// Connecting ///
		if( $this->bUseSockets )
		{
            if( !$this->resSocket = @socket_create( (($bIPv6) ? AF_INET6 : AF_INET), SOCK_STREAM, SOL_TCP ) )
			{
				throw new POP3_Exception("", self::ERR_SOCKETS);
			}
            $this->log( ($bIPv6) ? "AF_INET6" : "AF_INET" ."-TCP Socket created (using sockets extension)");

			$this->setSockTimeout($arrConnectionTimeout);
			
            if( !@socket_connect($this->resSocket, $this->strHostname, $this->intPort) )
			{
				throw new POP3_Exception("", self::ERR_SOCKETS);
			}            
            if( !@socket_getpeername($this->resSocket,$this->strIPAdress) )
            {
                throw new POP3_Exception("",self::ERR_SOCKETS);
            }
        }
		else
		{
            $dTimeout = (double) implode(".",$arrConnectionTimeout);
			if( !$this->resSocket = @fsockopen("tcp://". $strHostname .":". $intPort, &$intErrno, &$strError, $dTimeout) )
			{
				throw new POP3_Exception( "[". $intErrno."] -- ". $strError, self::ERR_STREAM );
			}
			
			$this->setSockTimeout($arrConnectionTimeout);
            $this->strIPAdress = @gethostbyname($strHostname);
		}
        $this->bSocketConnected = TRUE;
        $this->log("Connected to ". $this->strIPAdress .":". $this->intPort ." [". $this->strHostname ."]");
        
        // Get the first response with, if APOP support avalible, the apop banner.
        $strBuffer = "";
        $strBuffer = $this->recvString();
        $this->log($strBuffer);
        $this->parseBanner($strBuffer);
		$this->intCurState = self::STATE_AUTHORIZATION;
	}
    // }}}
	// {{{ disconnect()
    /*
	 * Disconnect from the server
	 *
	 * @return void
	 * @throw POP3_Exception
	 */
	public function disconnect()
	{
		if( $this->bSocketConnected )
        {
            if( $this->bUseSockets )
            {
                if( @socket_close($this->resSocket) === FALSE )
                {
                    throw new POP3_Exception("", self::ERR_SOCKETS);
                }
            }
            else
            {
                if( !@fclose($this->resSocket) )
			    {
				    throw new POP3_Exception("fclose(): Failed to close socket", self::ERR_STREAM);
			    }
            }
            $this->bSocketConnected = FALSE;
            $this->log("Disconneted from ". $this->strIPAdress .":". $this->intPort ." [". $this->strHostname ."]" );
        }
	}
    // }}}
    // {{{ login()
	/**
	 * Login
	 *
	 * @param string $strUser  Username
	 * @param string $strPass  Password
	 * @param boolean $bApop  APOP Authorization Mechanism
	 *
	 * @return void
	 * @throw POP3_Exception
	 */
	public function login( $strUser, $strPass, $bAPOP = FALSE)
	{
        if( !$this->checkState(self::STATE_AUTHORIZATION) ) 
        {
            throw new POP3_Exception("", self::ERR_INVALID_STATE);
        }

		if( !is_string($strUser) || strlen($strUser) == 0 )
		{
			throw new POP3_Exception("Invalid username string given", self::ERR_PARAMETER);
		}
		if( !is_string($strPass) )
		{
			throw new POP3_Exception("Invalid password string given", self::ERR_PARAMETER);
		}
		if( !is_bool($bAPOP) )
		{
			throw new POP3_Exception("Invalid APOP variable given", self::ERR_PARAMETER);
		}

		if( $this->bAPOPAutoDetect && !is_null($this->strAPOPBanner) && !$bAPOP)
		{
			$bAPOP = TRUE;
		}

		if( $bAPOP )
		{
			// APOP Auth
			$this->sendCmd("APOP ". $strUser ." ". hash("md5",$this->strAPOPBanner . $strPass, false));
		}
		else
		{
			// POP3 Auth
			$this->sendCmd( "USER ". $strUser, "USER ". (($this->bHideUsernameAtLog) ? hash("sha256",$strUser . microtime(true),false) : $strUser) );
			$this->sendCmd( "PASS ". $strPass, "PASS ". hash("sha256",$strPass . microtime(true),false) );
		}
		$this->intCurState = self::STATE_TRANSACTION;
    }
    // }}}
    // {{{ quit()
    /**
	 * Send the quit command to the server
	 *
	 * @return void
	 * @throw POP3_Exception
	 */
    public function quit()
    {
		if( $this->checkState(self::STATE_DISCONNECT) )
        {
            throw new POP3_Exception("", self::ERR_INVALID_STATE);
        }
        $this->sendCmd("QUIT");
	}
    // }}}
    // {{{ stat()
	public function stat()
	{
		if( !$this->checkState(self::STATE_TRANSACTION) ) throw new POP3_Exception("", self::ERR_INVALID_STATE);
        return $this->sendCmd("STAT");    
	}
    // }}}
    // {{{ retr()
    public function retr( $intMsgNum )
    {
		if( !$this->checkState(self::STATE_TRANSACTION) ) throw new POP3_Exception("", self::ERR_INVALID_STATE);
        if( !is_int($intMsgNum) )
		{
            throw new POP3_Exception("Invalid message number given", self::ERR_PARAMETER);
        }

		$this->sendCmd("RETR ". $intMsgNum );
		return $this->recvToPoint(); 
    }
    // }}}
    // {{{ list()
/*
 * problem: php thinks we use the list function here :? :?
 *
    public function list()
    {
		if( !$this->checkState(self::STATE_TRANSACTION) ) throw new POP3_Exception("", self::ERR_INVALID_STATE);
        $this->sendCmd("LIST");
		return $this->recvToPoint();
    }
*/
    // }}}
    // {{{ uidl()
    public function uidl()
    {
	    if( !$this->checkState(self::STATE_TRANSACTION) ) throw new POP3_Exception("", self::ERR_INVALID_STATE);
        $this->sendCmd("UIDL");
		return $this->recvToPoint();
    }
    // }}}
    // {{{ top()
    public function top( $intMsgNum , $intLines = 0 )
    {
        if( !$this->checkState(self::STATE_TRANSACTION) ) throw new POP3_Exception("", self::ERR_INVALID_STATE);
        if( !is_int($intMsgNum) )
        {
            throw new POP3_Exception("Invalid message number given", self::ERR_PARAMETER);
        }
        $this->sendCmd("TOP ". $intMsgNum ." ". $intLines);
        return $this->recvToPoint();
    }
    // }}}
    // {{{ dele
    /*
     * Mark a message as delete
     *
     * @param int $intMsgNum  Message Number
     */
    public function dele( $intMsgNum )
    {
        if( !$this->checkState(self::STATE_TRANSACTION) ) throw new POP3_Exception("", self::ERR_INVALID_STATE);
        if( !is_int($intMsgNum) )
        {
            throw new POP3_Exception("Invalid message number given", self::ERR_PARAMETER);
        }
        $this->sendCmd("DELE ". $intMsgNum);
        return;
    }
    // }}}
    // {{{ getMails()
    /*
     *
     * @param array $arrMsgNums  Numeric array with the message numbers
     *
     * @return array 
     */
    public function getMails( $arrMsgNums )
    {
        $arrMsgs = array();
        foreach( $arrMsgNums as $intMsgNum )
        {
            $arrMsgs[$intMsgNum] = $this->retr($intMsgNum);
        }
        return $arrMsgs;
    }
	// }}}
    // {{{ getOfficeStatus()
	public function getOfficeStatus()
	{
        if( !$this->checkState(self::STATE_TRANSACTION) ) throw new POP3_Exception("", self::ERR_INVALID_STATE);
    	$arrRes = array();

		$strSTATs = $this->stat();
        $arrSTATs = explode(" ",trim($strSTATs));
        $arrRes["count"] = (int) $arrSTATs[1];
		$arrRes["octets"] = (int) $arrSTATs[2];

        if( $arrRes["count"] > 0 )
        {
		    $strUIDLs = $this->uidl();
		    //$strLIST = $this->list();
            $this->sendCmd("LIST");
            $strLISTs = $this->recvToPoint();
        
            $arrUIDLs = explode("\r\n",trim($strUIDLs));
		    $arrLISTs = explode("\r\n",trim($strLISTs));
		
            for($i=1; $i<=$arrRes["count"]; $i++)
		    {
                list(,$intUIDL) = explode(" ", trim($arrUIDLs[$i-1]));
                list(,$intLIST) = explode(" ", trim($arrLISTs[$i-1]));
			    $arrRes[$i]["uidl"] = (int) $intUIDL;
			    $arrRes[$i]["octets"] = (int) $intLIST;
		    }
        }
		return $arrRes;
	}
    // }}}
    /////////////////////////////////////////////////////////////////////////////
    /////////////////////// Private functions ///////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    // {{{ checkState()
    /**
     * Check the current state with the needed. If they are equal the function return true else it return false.
     *
     * @param integer $intNeededState
     *
     * @return bool
     */
    private function checkState( $intNeededState )
    {
        return ($this->intCurState == $intNeededState) ? TRUE : FALSE;
    }
    // }}}
    // {{{ send()
    /**
     *
     */
	private function send( $strCmd )
    {
        $strCmd .= "\r\n";
        if( $this->bUseSockets )
        {
            if( @socket_send($this->resSocket, $strCmd, strlen($strCmd), 0) === FALSE )
            {
                throw new POP3_Exception("", self::ERR_SOCKETS);                
            }
        }
        else
        {
            if( !@fwrite($this->resSocket, $strCmd, strlen($strCmd)) )
            {
                throw new POP3_Exception("fwrite(): Failed to write string to socket",self::ERR_STREAM);
            }
        }
    }
    // }}}
    // {{{ sendCmd
    /*
     *
     * @param string $strLog  Workaround for non clear passwords and usernames in log file
     */
	private function sendCmd( $strCmd , $strLog = NULL )
	{
		( !is_null($strLog) ) ? $this->log($strLog) : $this->log($strCmd);
		$this->send($strCmd);
		$strRes = $this->recvString();
		$this->log($strRes);
        // 1. the check for the strlen of the result is a workaround for some server who don't send something after the quit command
        // 2. should run with qmailer too...qmailer bug (pop3.class.inc) "." instead of "+OK" after RETR command
		if( strlen($strRes) > 0 && $strRes{0} == '-' )
		{
			throw new POP3_Exception(trim($strRes), self::ERR_SEND_CMD);
		}
        return $strRes;
	}
    // }}}
    // {{{ recv()
    /**
     *Return value:
     * -----------------------------

     a) on success returns number of bytes read

     b) in case of no data on line, returns zero and $buf will be set to NULL.

     c) on failure returns false, and $buf will be set to NULL.
     To get the error code/message, call the appropriate socket functions.

     d) in case of disconnect, the function returns either b) or c) which depends on how connection was closed from the other end. It returns 0 if the connection was closed gracefully with FIN squence and false if it was reset.
     *
     */
    private function recv( &$strBuffer, $intBufferSize = 4096 )
    {
		$strBuffer = "";
        if( $this->bUseSockets )
        {
            $intReadBytes = @socket_recv($this->resSocket, $strBuffer, $intBuferSize, 0);
            var_dump($intReadBytes);
            if( $intReadBytes < 0 )
            {
                throw new POP3_Exception("", POP3::ERR_SOCKETS);
            }
            
		}
        else
        {
            if( !$strBuffer = @fread($this->resSocket, $intBufferSize) )
			{
				throw new POP3_Exception("fread(): Couldn't recieve from socket", self::ERR_STREAM);
			}
        }
		return $intReadBytes;
    }
    // }}}
    // {{{ recvString()
	private function recvString( $intBufferSize = self::DEFAULT_BUFFER_SIZE )
	{
		$strBuffer = "";
		if( $this->bUseSockets )
		{
			if( ($strBuffer = @socket_read($this->resSocket, $intBufferSize , PHP_NORMAL_READ)) === FALSE )
			{
				throw new POP3_Exception("", self::ERR_SOCKETS);
			}
            // Workaround because socket_read with PHP_NORMAL_READ stop at "\r" but the network string ends witn "\r\n"
            // so we need to call again to get the "\n"
            if( ($strBuffer2 = @socket_read($this->resSocket, 1 , PHP_NORMAL_READ)) === FALSE )
            {
                throw new POP3_Exception("", self::ERR_SOCKETS);
            }
        }
		else
		{
			if( !$strBuffer = @fgets($this->resSocket, $intBufferSize) )
			{
				throw new POP3_Exception("fgets(): Couldn't recieve the string", self::ERR_STREAM);
			}
		}
		return $strBuffer;
	}
    // }}}
    // {{{ recvToPoint()	
	private function recvToPoint()
	{
		$strRes = "";
		while(true)
		{
			$strBuffer = $this->recvString();
            $strRes .= $strBuffer;
			if( $strBuffer{0} == '.' )
			{
				break;
			}
		}
		return $strRes;
	}
    // }}}
    // {{{ setSockTimeout()
    /**
     * @param array $arrTimeout  "sec" => seconds, "usec" => microseconds
     */
    private function setSockTimeout( $arrTimeout )
    {
        if( !is_array($arrTimeout) || !is_int($arrTimeout["sec"]) || !is_int($arrTimeout["usec"]) )
        {
            throw new POP3_Exception("Invalid Connection Timeout given", self::ERR_PARAMETER);
        }

   	    if( $this->bUseSockets )
	    {
            if( !@socket_set_option($this->resSocket,SOL_SOCKET, SO_RCVTIMEO, $arrTimeout)
                || !@socket_set_option($this->resSocket,SOL_SOCKET, SO_SNDTIMEO, $arrTimeout) )
            {
                throw new POP3_Exception("", POP3::ERR_SOCKETS);
            }
	    }
        else
        {
            if( !@stream_set_timeout($this->resSocket, $arrTimeout["sec"], $arrTimeout["usec"]) )
            {
                throw new POP3_Exception("stream_set_timeout(): Failed to set stream timeout", self::ERR_STREAM);
            }
        }
        $this->log("Set socket timeout to ". implode(".",$arrTimeout) ." secondes.");
    }
    // }}}
    // {{{ parseBanner()
    private function parseBanner( $strBuffer )
    {
        //$strBuffer = ""; //$this->recvString();
        $intBufferLength = strlen($strBuffer);
        $bOpenTag = FALSE;
        for( $i=0; $i < $intBufferLength; $i++ )
        {
            if( $strBuffer{$i} === '>' )
            {
                break;
            }
            if( $bOpenTag )
            {
                $this->strAPOPBanner .= $strBuffer{$i};
                continue;
            }
            if( $strBuffer{$i} === '<' )
            {
                $bOpenTag = TRUE;
            }


        }
//        var_dump("APOP Banner: ". $this->strAPOPBanner);
    }
    // }}}
    
    /**
     * // LOGGING FUNCTIONS
     */
    // {{{ openlog()
	private function openlog()
	{
		if( !$this->bLogOpened && !is_null($this->strLogFile) )
		{
			if( !$this->resLogFp = @fopen($this->strLogFile,"a+") )
			{
				throw new POP3_Exception("Couldn't open log file: ". $this->logFile, self::ERR_LOG);
			}
			$this->bLogOpened = TRUE;
		}
	}
    // }}}
    // {{{ closelog()
	private function closelog()
	{
		if( $this->bLogOpened )
		{
			@fclose($this->resLogFp);
			$this->bLogOpened = FALSE;
		}
	}
	// }}}
    // {{{ log()
	private function log( $str )
	{
		if( $this->bLogOpened )
		{
		    $str = date("Y-m-d H:i:s") .": ". trim($str) . PHP_EOL;
			if( !@fwrite( $this->resLogFp, $str, strlen($str) ) )
			{
				return new POP3_Exception("Failed to wrote to log file. (" . trim($str) . ")", self::ERR_LOG);
			}
		}
	}
    // }}}
}
// }}}
//////////////////////////////////////////////////////////////////////
//////////////////////// EXPERIMENTAL ////////////////////////////////
//////////////////////////////////////////////////////////////////////
// {{{ POP3_Result
class POP3_Result
{
    // {{{ private attributes
    private $intMsgNum = 0;
    private $arrHeader = array();
    private $arrBody = array();
    private $arrAttachments = array();
    // }}}
    // {{{ __construct()
    function __construct( $intMsgNum, $strMessage )
    {
        $this->intMsgNum = $intMsgNum;
        $this->parseMessage($strMessage);
    }
    // }}}
    // {{{ __destruct()
    function __destruct()
    {
        $this->intMsgNum = 0;
        $this->arrHeader = NULL;
        $this->arrBody = NULL;
        $this->arrAttachments = NULL;
    }
    // }}}
    // {{{ getMessageNum()
    public function getMessageNum()
    {
        return $this->intMsgNum;
    }
    // }}}
    // {{{ getHeader()
    public function getHeader( $bAsArray = FALSE )
    {
        if( !$bAsArray )
        {
            $strHeader = "";
            foreach($this->arrHeader AS $strHeadLine )
            {
                $strHeader .= $strHeadLine;
            }
            return $strHeader;
        }
        return $this->arrHeader;
    }
    // }}}
    // {{{ getBody()
    public function getBody()
    {
        if( !$bAsArray )
        {
            $strBody = "";
            foreach($this->arrBody AS $strBodyLine )
            {
                $strBody .= $strBodyLine;
            }
            return $strBody;
        }
        return $this->arrBody;
    }
    // }}}
    // {{{ __toString()
    function __toString()
    {
        return $this->getHeader() . $this->getBody() . $this->getAttachments();
    }
    // }}}
    // {{{ getAttachment()
    public function getAttachment( $intAttchmentNum = 0 )
    {   
    }
    // }}}
    // {{{ getAttachments()
    public function getAttachments()
    {
    }
    // }}}
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////// PRIVATE FUNCTIONS ///////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // {{{ parseMessage()
    private function parseMessage( &$strMessage )
    {
    }
    // }}}
    // {{{ ()
    // }}}
}
// }}}
?>
