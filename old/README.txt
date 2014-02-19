-----------------------------------------------------------------------------------------------

-- Created this file on 03/30/2005 to capture changes and development status.

-- Holding Period Violation Updates Issue: Trades vanishing despite daily updates.
   modified admin_prepare.php for the following:
		$qry_statement = "delete from Trades_m where trdm_trade_date = '".$max_trade_date."' and trdm_auto_id != '30092' and trdm_auto_id != '30069' and trdm_auto_id != '30034' and trdm_auto_id != '14187'";
		$exec_query = mysql_query($qry_statement) or die (mysql_error());

-- Javascript Calendar
   Fixed in user_mgmt.php
	 Both create and edit interface/forms
	 
---------------------------------DONE--------------------------------
-- TO ADD (RATAN): in my preferences: Reports via Email : Yes/No  & Frequency
   
-- TO MODIFY : Menu for User Add/Manage FOR ADMINS ONLY
-- TO MODIFY : ADMIN CAN DESIGNATE OTHER ADMINS, FEATURE SHOULD BE IN USER ADD/EDIT.
-- FIX YES/1 in user receive emails, lets stick to 1/0 only
 
 -- CREATE USER SHOULD HAVE DEFAULT DATA ENTERED.
 ---------------------------------DONE--------------------------------

 -- MAKE CHANGES WITH B/S IN TRADES REPORT
 