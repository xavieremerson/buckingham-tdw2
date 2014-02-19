<?php
/************************************************************************
MyPagina ver. 1.02
Use this class to handle MySQL record sets and get page navigation links 

Copyright (c) 2005, Olaf Lederer
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the finalwebsites.com nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

_________________________________________________________________________
available at http://www.finalwebsites.com/
Comments & suggestions: http://www.finalwebsites.com/contact.php

Updates & bugfixes

ver. 1.01 - There was a small bug inside the page_info() method while showing
the last page (set). The error (last record) is fixed. There is also a small 
update in the method set_page(), the check is now with $_REQUEST values in 
place of $_GET values.

ver. 1.02 - The link text (and the new image function) for the forward and backward links will be created with the new method build_back_or_forward(). Because there is no need anymore the variables str_forward and str_backward are removed. Check the example file for the possibility to  use images in place of strings for the back- and forward navigation and the modified navigation() method.

*************************************************************************/
//require("includes/dbconnect.php");

//=============================================================================================================
// modify these constants to fit your environment
if (!defined("DB_SERVER")) define("DB_SERVER", "localhost");
if (!defined("DB_NAME")) define("DB_NAME", "warehouse");
if (!defined("DB_USER")) define ("DB_USER", "newadmin");
if (!defined("DB_PASSWORD")) define ("DB_PASSWORD", "newpassword");

// some external constants to controle the output
define("QS_VAR", "page"); // the variable name inside the query string (don't use this name inside other links)
define("NUM_ROWS", 30); // the number of records on each page

define("STR_FWD", "&gt;&gt;"); // the string is used for a link (step forward)
define("STR_BWD", "&lt;&lt;"); // the string is used for a link (step backward)

// use the rught pathes to get it working with the php function getimagesize
define("IMG_FWD", "images/paginate/forward.gif"); // the image for forward link 
define("IMG_BWD", "images/paginate/backward.gif"); // the image for backward link 

define("NUM_LINKS", 10); // the number of links inside the navigation (the default value)
//=============================================================================================================

//error_reporting(E_ALL); // only for testing

class Pagination {
	
	var $sql;
	var $result;
	
	var $get_var = QS_VAR;
	var $rows_on_page = NUM_ROWS;

	var $all_rows;
	var $num_rows;
	
	var $page;
	var $number_pages;
	
	// constructor
	function MyPagina() {
		$this->connect_db();
	}
	// sets the current page number
	function set_page() {
		$this->page = (isset($_REQUEST[$this->get_var]) && $_REQUEST[$this->get_var] != "") ? $_REQUEST[$this->get_var] : 0;
		return $this->page;
	}
	// gets the total number of records 
	function get_total_rows() {
		$tmp_result = mysql_query($this->sql);
		$this->all_rows = mysql_num_rows($tmp_result);
		mysql_free_result($tmp_result);
		return $this->all_rows;
	}
	// database connection
	function connect_db() {
		$conn_str = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
		mysql_select_db(DB_NAME, $conn_str);
	}
	// get the totale number of result pages
	function get_num_pages() {
		$this->number_pages = ceil($this->get_total_rows() / $this->rows_on_page);
		return $this->number_pages;
	}
	// returns the records for the current page
	function get_page_result() {
		$start = $this->set_page() * $this->rows_on_page;
		$page_sql = sprintf("%s LIMIT %s, %s", $this->sql, $start, $this->rows_on_page);
		$this->result = mysql_query($page_sql);
		return $this->result;
	}
	// get the number of rows on the current page
	function get_page_num_rows() {
		$this->num_rows = mysql_num_rows($this->result);
		return $this->num_rows;
	}
	// free the database result
	function free_page_result() {
		mysql_free_result($this->result);
	}
	// function to handle other querystring than the page variable
	function rebuild_qs($curr_var) {
		if (!empty($_SERVER['QUERY_STRING'])) {
			$parts = explode("&", $_SERVER['QUERY_STRING']);
			$newParts = array();
			foreach ($parts as $val) {
				if (stristr($val, $curr_var) == false)  {
					array_push($newParts, $val);
				}
			}
			if (count($newParts) != 0) {
				$qs = "&".implode("&", $newParts);
			} else {
				return false;
			}
			return $qs; // this is your new created query string
		} else {
			return false;
		}
	} 
	// this method will return the navigation links for the conplete recordset
	function navigation($separator = " | ", $css_current = "", $back_forward = false, $use_images = true) {
		$max_links = NUM_LINKS;
		$curr_pages = $this->set_page(); 
		$all_pages = $this->get_num_pages() - 1;
		$var = $this->get_var;
		$navi_string = "";
		if (!$back_forward) {
			$max_links = ($max_links < 2) ? 2 : $max_links;
		}
		if ($curr_pages <= $all_pages && $curr_pages >= 0) {
			if ($curr_pages > ceil($max_links/2)) {
				$start = ($curr_pages - ceil($max_links/2) > 0) ? $curr_pages - ceil($max_links/2) : 1;
				$end = $curr_pages + ceil($max_links/2);
				if ($end >= $all_pages) {
					$end = $all_pages + 1;
					$start = ($all_pages - ($max_links - 1) > 0) ? $all_pages  - ($max_links - 1) : 1;
				}
			} else {
				$start = 0;
				$end = ($all_pages >= $max_links) ? $max_links : $all_pages + 1;
			}
			if($all_pages >= 1) {
				$forward = $curr_pages + 1;
				$backward = $curr_pages - 1;
				// the text two labels are new sinds ver 1.02
				$lbl_forward = $this->build_back_or_forward("forward", $use_images);
				$lbl_backward = $this->build_back_or_forward("backward", $use_images);
				$navi_string = ($curr_pages > 0) ? "<a class=\"pagination\" href=\"".$_SERVER['PHP_SELF']."?".$var."=".$backward.$this->rebuild_qs($var)."\">".$lbl_backward."</a>&nbsp;" : $lbl_backward."&nbsp;";
				if (!$back_forward) {
					for($a = $start + 1; $a <= $end; $a++){
						$theNext = $a - 1; // because a array start with 0
						if ($theNext != $curr_pages) {
							$navi_string .= "<a class=\"pagination\" href=\"".$_SERVER['PHP_SELF']."?".$var."=".$theNext.$this->rebuild_qs($var)."\">";
							$navi_string .= "&nbsp;".$a."&nbsp;</a>";
							$navi_string .= ($theNext < ($end - 1)) ? $separator : "";
						} else {
							$navi_string .= ($css_current != "") ? "<span class=\"".$css_current."\">&nbsp;".$a."&nbsp;</span>" : $a;
							$navi_string .= ($theNext < ($end - 1)) ? $separator : "";
						}
					}
				}
				$navi_string .= ($curr_pages < $all_pages) ? "&nbsp;<a class=\"pagination\" href=\"".$_SERVER['PHP_SELF']."?".$var."=".$forward.$this->rebuild_qs($var)."\">".$lbl_forward."</a>" : "&nbsp;".$lbl_forward;
			}
		}
		return $navi_string;
	}
	// function to create the back/forward elements; $what = forward or backward
	// type = text or img
	function build_back_or_forward($what, $img = true) {
		$label['text']['forward'] = STR_FWD;
		$label['text']['backward'] = STR_BWD;
		$label['img']['forward'] = IMG_FWD;
		$label['img']['backward'] = IMG_BWD;
		if ($img) {
			$img_info = getimagesize($label['img'][$what]);
			$label = "<img src=\"".$label['img'][$what]."\" ".$img_info[3]." border=\"0\">";
		} else {
			$label = $label['text'][$what];
		}
		return $label;
	}
	// this info will tell the visitor which number of records are shown on the current page
	function page_info($to = "-") {
		$first_rec_no = ($this->set_page() * $this->rows_on_page) + 1;
		$last_rec_no = $first_rec_no + $this->rows_on_page - 1;
		$last_rec_no = ($last_rec_no > $this->get_total_rows()) ? $this->get_total_rows() : $last_rec_no;
		$to = trim($to);
		$info = $first_rec_no." ".$to." ".$last_rec_no;
		return $info;
	}
	// simple method to show only the page back and forward link.
	function back_forward_link($images = false) {
		$simple_links = $this->navigation(" ", "", true, $images);
		return $simple_links;
	}
}
?>