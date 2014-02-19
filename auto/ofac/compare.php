<?		
include('../../includes/dbconnect.php');				
include('../../includes/functions.php');				

echo levenshtein('TEST', 'TESTES');
similar_text('TEST', 'TESTES', $p);
echo "Percent: $p%"
?>