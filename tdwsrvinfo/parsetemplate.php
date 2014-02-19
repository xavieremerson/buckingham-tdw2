<?php

class ParseTemplate
	{
	var $Template;

	function loadTemplate ($File)
		{
		@$fileFp = fopen("./templates/".$File, "r");
		if ($fileFp)
			{
			$template = fread($fileFp, filesize("./templates/".$File));
			fclose($fileFp);
			$this->Template = $template;
			return true;
			}
			else
			{
			return false;
			}
		}


	function getPiece ($charBegin, $charEnd)
		{
		$pieceStart = strpos($this->Template, $charBegin) + strlen($charBegin);
		$pieceEnd = (strpos($this->Template, $charEnd) + strlen ($charEnd));
		if ((!$pieceStart) || (!$pieceEnd))
			{
			return false;
			}
			else
			{
			$pieceLength = $pieceEnd - $pieceStart;
			$piece = substr ($this->Template, $pieceStart, $pieceLength);
			$begin = substr ($this->Template, 0, $pieceStart);
			$end = substr ($this->Template, $pieceEnd);
			}
		return array ($piece, $begin, $end);
		}


	function compileSection ($head, $piece, $foot)
		{
		$return = $head;
		$return .= $piece;
		$return .= $foot;
		$this->Template = $return;
		}


	function replace ($search, $replace, $string = "")
		{
		if ($string == "")
			{
			$this->Template = str_replace($search, $replace, $this->Template);
			}
			else
			{
			return str_replace ($search, $replace, $string);
			}
		}


	function display ()
		{
		return $this->Template;
		}

	}
?>