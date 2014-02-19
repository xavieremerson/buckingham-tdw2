<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!-- Fig. 26.7: compare.php -->
<!-- String Comparison      -->

<html xmlns = "http://www.w3.org/1999/xhtml">
   <head>
      <title>String Comparison</title>
   </head>

   <body>
      <?php 

         // create array fruits
         $fruits = array( "apple", "orange", "banana" );
      
         // iterate through each array element
         for ( $i = 0; $i < count( $fruits ); $i++ ) {

            // call function strcmp to compare the array element
            // to string "banana"
            if ( strcmp( $fruits[ $i ], "banana" ) < 0 )
               print( $fruits[ $i ]." is less than banana " );
            elseif ( strcmp( $fruits[ $i ], "banana" ) > 0 ) 
               print( $fruits[ $i ].
                  " is greater than banana " ); 
            else 
               print( $fruits[ $i ]." is equal to banana " );
         
            // use relational operators to compare each element
            // to string "apple"
            if ( $fruits[ $i ] < "apple" ) 
              print( "and less than apple! <br />" );
            elseif ( $fruits[ $i ] > "apple" ) 
              print( "and greater than apple! <br />" );
            elseif ( $fruits[ $i ] == "apple" ) 
               print( "and equal to apple! <br />" );
         
         }
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