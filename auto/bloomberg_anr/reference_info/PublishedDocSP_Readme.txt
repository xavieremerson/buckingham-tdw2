/*************************************************************************
*** 12/23/2004
*** prGetAllPublishedDocIds
*** prGetAllTickersInNote
*** prGetAllIndustriesInNote
*** prGetAllAnalystsInNote
*** prGetRelatedDocIdsInCompendium
***************************************************************************/

1. To get a list of DocId, ProductName, StatusDateTime, Status
for all published notes as of a certain date:
prGetAllPublishedDocIds {date}

2. If the note is a Compendium Type, use DocId to get the DocIds that make up the notes in the Compendium:
prGetRelatedDocIdsInCompendium {docId}

3. Use DocId of the note (not the Compendium) as parameter to retrieve the analysts, industries and tickers:
prGetAllIndustriesInNote {docId}
prGetAllAnalystsInNote {docId}
prGetRelatedDocIdsInCompendium {docId}

/**************************************************************************
*** prGetAllPublishedDocIds:  sp to get list of all published notes
*** Returns ResultSet containing DocId, ProductName, StatusDateTime, Status
*** for all notes with a published status
*** Sample Usage:
*** EXEC prGetAllPublishedDocIds '12/21/2004 09:59:46 AM' 
*** EXEC prGetAllPublishedDocIds (all)
***************************************************************************/

/**************************************************************************
*** prGetAllTickersInNote: sp to get list of all industries in the note
*** Returns ResultSet containing Ticker
*** for all Issuers in the note indicated by the DocId
*** Returns results for Issuer-based or Industry-based note (not Compendium)
*** Sample Usage:
*** EXEC prGetAllTickersInNote 'ABC_20041220_CR'
***************************************************************************/

/**************************************************************************
*** prGetAllIndustriesInNote: sp to get list of all industries in the note
*** Returns ResultSet containing IndustryName
*** for all Industries in the note indicated by the DocId
*** Returns results for Issuer-based or Industry-based note (not Compendium)
*** Sample Usage:
*** EXEC prGetAllIndustriesInNote 'ABC_20041220_CR'
***************************************************************************/

/**************************************************************************
***  prGetAllAnalystsInNote: sp to get list of all analysts in the note :
*** Returns ResultSet containing DisplayName, LastName, FirstName
*** for all Analysts in the note indicated by the DocId
*** Returns results for Issuer-based or Industry-based note (not Compendium)
*** Sample Usage:
*** EXEC prGetAllAnalystsInNote 'ABC_20041220_CR'
***************************************************************************/

/**************************************************************************
*** prGetRelatedDocIdsInCompendium sp to get list of all DocIds in the Compendium
*** Returns ResultSet containing DocIds
*** for all Notes in the Compendium indicated by the DocId parameter
*** Returns results for Compendium type note only
*** Sample Usage:
*** EXEC prGetRelatedDocIdsInCompendium 'MMN_12212004'
***************************************************************************/