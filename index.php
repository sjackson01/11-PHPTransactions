<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Transfer Funds</title>
    </head>
    <body>
        <h1>Transfer Funds</h1>
        <?php # Script 11.7 - transfer.php
            // This page performs a transfer of funds from one account to the other.
            // This page uses transactions
            
            $dbc = mysqli_connect('mysql:3306', 'root', 'tiger', 'banking') 
            OR die ('Could not connect to MySQL: ' . mysqli_connect_error());

            // Check if the form has been submitted
            if ($_SEVER['REQUEST_METHOD'] == 'POST'){

                //Minimal form validation
                if (isset($_POST['from'], $_POST['to'], $_POST['amount']) 
                && is_numeric($_POST['from']) 
                && is_numeric($_POST['to']) 
                && is_numeric($_POST['amount'])){ 

                $from = $_POST['from'];
                $to = $_POST['to'];
                $amount =  $_POST['amount'];
            }

            //Make sure enough funds are availble
            $q = "SELECT balance FROM accounts WHERE account_id=$from";
            $r = @mysqli_query($dbc, $q);
            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);

            if ($amount > $row['balance']){
                echo '<p class="error">Insufficient funds to complete the transfer </p>';
                }else{
                    // Turn autocommit off
                mysqli_autocommit($dbc, FALSE);

                $q = "UPDATE accounts SET balance=balance-$amount WHERE account_id=$from";
                $r = @mysqli_query($dbc, $q);
                if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

                $q = "UPDATE accounts SET balance=balance+$amount WHERE account_id=$to";  
                $r = @mysqli_query($dbc, $q);

            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

                mysqli_commit($dbc);
                echo '<p>The transfer was a success!</p>';

            } else {
            mysqli_rollback($dbc);
            echo '<p>The transfer could not be made due to a system error. We apologize for any inconvenience.</p>'; // Public message.
            echo '<p>' . mysqli_error($dbc) . '<br>Query: ' . $q . '</p>'; // Debugging message.
            }
                                        
            } else { // Invalid submitted values.
            echo '<p>Please select a valid "from" and "to" account and enter a numeric amount to transfer.</p>';
            } break;
            } // End of submit conditional.
            // Always show the form...
                                        
            // Get all the accounts and balances as OPTIONs for the SELECT menus:
            $q = "SELECT account_id, CONCAT(last_name, ', ', first_name) AS name, type, balance FROM
            accounts LEFT JOIN customers USING (customer_id) ORDER BY name";

            $r = @mysqli_query($dbc, $q);
            $options = '';
                                      
            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            $options .= "<option value=\"{$row['account_id']}\">{$row['name']} ({$row['type']})\${$row['balance']}</option>\n";
            }
        }
            
        
        ?>
    </body>
</html>