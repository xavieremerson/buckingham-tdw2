<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!-- Fig. 26.8: expression.php -->
<!-- Using regular expressions -->

<html xmlns = "http://www.w3.org/1999/xhtml">
   <head>
      <title>Regular expressions</title>
   </head>

   <body>
      <?php
         $search = "Now is the time";
         print( "Test string is: '$search'<br /><br />" );

         // call function ereg to search for pattern ’Now’
         // in variable search
         if ( ereg( "Now", $search ) )
            print( "String 'Now' was found.<br />" );

         // search for pattern ’Now’ in the beginning of 
         // the string
         if ( ereg( "^Now", $search ) ) 
            print( "String 'Now' found at beginning 
               of the line.<br />" );
            
         // search for pattern ’Now’ at the end of the string
         if ( ereg( "Now$", $search ) ) 
            print( "String 'Now' was found at the end 
               of the line.<br />" ); 
            
         // search for any word ending in ’ow’
         if ( ereg( "[[:<:]]([a-zA-Z]*ow)[[:>:]]", $search,
            $match ) ) 
            print( "Word found ending in 'ow': " .
               $match[ 1 ] . "<br />" );
            
         // search for any words beginning with ’t’
         print( "Words beginning with 't' found: ");

         while ( eregi( "[[:<:]](t[[:alpha:]]+)[[:>:]]",
            $search, $match ) ) {
            print( $match[ 1 ] . " " );

           // remove the first occurrence of a word beginning 
           // with ’t’ to find other instances in the string
           $search = ereg_replace( $match[ 1 ], "", $search );
         }   

         print( "<br />" );
      ?>
   </body>
</html>

<!--
**************************************************************************
* (C) Copyright 1992-2004 by Deitel & Associates, Inc. and               *
* Pearson Education, Inc. All Rights Reserved.                           *
*                                                                        *
* DISCLAIMER: The authors and publisher of this book have used their     *
* best efforts in preparing the book. These efforts include the          *
* development, research, and testing of the theories and programs        *
* to determine their effectiveness. The authors and publisher make       *
* no warranty of any kind, expressed or implied, with regard to these    *
* programs or to the documentation contained in these books. The authors *
* and publisher shall not be liable in any event for incidental or       *
* consequential damages in connection with, or arising out of, the       *
* furnishing, performance, or use of these programs.                     *
**************************************************************************
-->