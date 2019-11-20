

<html>
<head> 
<title>Список заказов</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="shortcut icon" href="image/logo.png" type="img/png">
<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
</head> 
<body style="margin: 0 auto;width: 1000px;background: #D3DCE3;">      
<?   
$db=mysqli_connect ('localhost', 'root','','BD');  
$query="select*from orders";
$result=mysqli_query($db, $query);
$num_results=mysqli_num_rows($result);
echo '<form action="form.php" method="POST">';    
echo '<table class="table table-hover table-dark" style="border-radius: 4px;">
      <thead>
      <tr>
      <th scope="col">№</th>
      <th scope="col">Названием товара</th>
      <th scope="col">Цена</th>
      <th scope="col">Статус</th>
      <th scope="col"></th>
      </tr>
      </thead>
      <tbody>';
for ($i=0; $i<$num_results; $i++)
{
$roww=mysqli_fetch_assoc($result);  
echo '<tr>
      <td>'.$roww['id'].'</td>
      <td>'.$roww['name'].'</td>
      <td>'.$roww['price'].'</td>
      <td>'.$roww['status'].'</td>
      <td><input class="btn btn-secondary btn-sm" type="submit" value="Оплатить" name="isopen['.$roww['id'].']" ></td>
      </tr>';         
} 
     '</tbody>
      </table>';
    '</form'; 
$isopen=$_POST["isopen"];    
define('USERNAME', 'sfedot-api');
define('PASSWORD', 'sfedot');
define('GATEWAY_URL', 'https://3dsec.sberbank.ru/payment/rest/');
define('RETURN_URL', 'http://localhost/test/form.php'); 
if(isset($isopen))
{
$idst = array_keys($isopen);  
$query="SELECT id,price FROM orders WHERE id IN('".implode("','", $idst)."')";  
$result=mysqli_query($db, $query);
$num_results=mysqli_num_rows($result);
for ($i=0; $i<$num_results; $i++)
{
$row=mysqli_fetch_assoc($result);  
 '<td>'.$row['id'].'</td>';
 '<td>'.$row['price'].'</td>';    
$num_results=mysqli_num_rows($result);  
}
$id = $row['id'];   
$price = $row['price'];     
}
$query="UPDATE orders SET status='paid' WHERE id='".$id."'";
$result=mysqli_query($db, $query);
function gateway($method, $data) {
$curl = curl_init(); // Инициализируем запрос
curl_setopt_array($curl, array(
CURLOPT_URL => GATEWAY_URL.$method, // Полный адрес метода
CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
));
$response = curl_exec($curl); // Выполненяем запрос
$output = curl_exec($curl);
if ($output === FALSE) {
echo "cURL Error: " . curl_error($curl);
}
$response = json_decode($response, true); // Декодируем из JSON в массив
curl_close($curl); // Закрываем соединение
return $response; // Возвращаем ответ
}   
if ($_SERVER['REQUEST_METHOD'] == 'POST') {   
    $data = array(
        'userName' => USERNAME,
        'password' => PASSWORD,
        'orderNumber' => $id,
        'amount' => $price,
        'returnUrl' => RETURN_URL
    );
    $response = gateway('register.do', $data);   
    if (isset($response['errorCode'])) { // В случае ошибки вывести ее
        echo 'Ошибка #' . $response['errorCode'] . ': ' . $response['errorMessage'];
    } else { // В случае успеха перенаправить пользователя на платежную форму
        header('Location: ' . $response['formUrl']);
        die();
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['orderId'])){
    $data = array(
        'userName' => USERNAME,
        'password' => PASSWORD,
        'orderId' => $_GET['orderId']
    );
    $response = gateway('getOrderStatus.do', $data);   
}   
?>  
</body>
  <script src="jquery/jquery-3.4.1.slim.min.js" ></script>
    <script src="popper.min.js" ></script>
    <script src="js/bootstrap.min.js"></script>    
</html> 
