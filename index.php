#!/usr/bin/php
<?php

function validate_phone_number($phone)
{
  $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
  $phone_to_check = str_replace("-", "", $filtered_phone_number);
  if (strlen($phone_to_check) < 9 || strlen($phone_to_check) > 14) {
    return false;
  } else {
    return true;
  }
}

function validate_phone($new_phone)
{
  if (validate_phone_number($new_phone) == true) {
    echo "Phone number is valid";
    $g = 1;
  } else {
    echo "Invalid phone number";
    $g = 0;
  }
  return $g;
}

function validate_name($new_name)
{
  if (preg_match('/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð][a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð -]+$/', $new_name)) {
    echo "\n\rName: " . $new_name . " is valid\n\r\n\r";
    $g = 1;
  } else {
    echo "\n\rInvalid name given;\n\r\n\r";
    $g = 0;
  }
  return $g;
}

function validate_email($new_email)
{
  if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    echo "\n\rEmail" . $new_email . " is valid.\n\r\n\r";
    $g = 1;
  } else {
    echo "\n\rInvalid email given;\n\r\n\r";
    $g = 0;
  }
  return $g;
}

$file = "visitors.csv";

if (file_exists($file)) {
  $csv = array_map('str_getcsv', file($file));
  // array_walk($csv, function (&$arr) use ($csv) {
  //     $arr = array_combine($csv[0], $arr);
  // });
  // array_shift($csv); # remove column header
  // var_dump($csv);
  // echo $csv[array_key_last($csv)][0];
  $id = intval($csv[array_key_last($csv)][0]) + 1;
} else {
  $id = 1;
  $fp = fopen($file, 'a');
  fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
  fclose($fp);
}

echo "Pick option: \r\n 1 - Enter new visitor \r\n 2 - Edit visitor \r\n 3 - delete visitor";
echo "\r\n\r\n";

do {
  $a = (int)readline("Enter option number: ");
  if (ctype_digit("$a")) {
    $i = 1;
  } else {
    $i = 0;
  }
} while ($i == 0);

switch ($a) {
  case 1:

    echo "Your ID: " . $id . "\n\r";
    do {
      $givenName = readline('Enter your full name: ');
      $i = validate_name($givenName);
    } while ($i == 0);

    do {
      $email = readline('Enter your email:');
      $i = validate_email($email);
    } while ($i == 0);

    do {
      $phone = readline('Enter your phone number:');
      $i = validate_phone($phone);
    } while ($i == 0);

    $fp = fopen($file, 'a');

    $date = date("Y-m-d H:i:s");
    
    echo "\n\rEnterence time: " . $date . "\n\r";

    fputcsv($fp, array($id, $givenName, $email, $phone, $date));
    fclose($fp);

    break;
  case 2:

    $d = 0;
    do {
      $visitor_id = (int)readline("Enter valid visitor ID (0 - to cancel search): \n\r");
      if (ctype_digit("$visitor_id")) {
        if (array_key_exists($visitor_id - 1, $csv) && 0 != $visitor_id) {
          echo "ID Exists, Visitor details:\n\r\n\r";
          echo "ID: " . $csv[$visitor_id - 1][0] . "; Name: " . $csv[$visitor_id - 1][1] . "; E-mail: " . $csv[$visitor_id - 1][2] . "; Phone: " . $csv[$visitor_id - 1][3] . " \n\r\n\r";
          do {
            echo "Enter what to do with the " . $visitor_id . " visitor: \n\r 1 - edit Name;\n\r 2 - edit E-mail;\n\r 3 - edit phone number  \n\r 4 - remove \n\r 5 - save changes to file \n\r 0 - nothing \n\r";
            $edit_pick = (int)readline("");
            if (ctype_digit("$edit_pick")) {
              switch ($edit_pick) {
                case 0:
                  break;
                case 1:
                  echo "Picked edit Visitor Name.\n\r";
                  do {
                    $givenName = readline('Enter ' . $csv[$visitor_id - 1][1] . ' Visitor new full name: ');
                    $j = validate_name($givenName);
                    if ($j == 1) {
                      $csv[$visitor_id - 1][1] = $givenName;
                    }
                  } while ($j == 0);
                  break;
                case 2:
                  echo "Picked edit Visitor E-mail.\n\r";
                  do {
                    $email = readline('Enter ' . $csv[$visitor_id - 1][2] . ' Visitor new email:');
                    $j = validate_email($email);
                    if ($j == 1) {
                      $csv[$visitor_id - 1][2] = $email;
                    }
                  } while ($j == 0);
                  break;
                case 3:
                  echo "Picked edit Visitor Phone number.\n\r";
                  do {
                    $phone = readline('Enter ' . $csv[$visitor_id - 1][3] . ' Visitor new phone number:');
                    $j = validate_phone($phone);
                    if ($j == 1) {
                      $csv[$visitor_id - 1][3] = $phone;
                    }
                  } while ($j == 0);
                  break;
                case 4:
                  echo "Picked to " . $csv[$visitor_id - 1][1] . " remove visitor.\n\r";

                  unset($csv[$visitor_id - 1]);
                  $csv = array_values($csv);

                  $valid = (int)readline("Are you sure? The Changes will be saved. (0-No; 1-Yes) \n\r");
                  if ($valid == 1) {
                    $fout = fopen($file, 'w');
                    foreach ($csv as $line) {
                      fputcsv($fout, $line);
                    }
                    fclose($fout);
                    $edit_pick = 0;
                  }

                  break;
                case 5:
                  $fout = fopen($file, 'w');
                  foreach ($csv as $line) {
                    fputcsv($fout, $line);
                  }
                  fclose($fout);
                  $edit_pick = 0;
                  break;
              }
            } elseif (intval($edit_pick) == 0) {
              $d = 1;
            } else {
              echo "Visitor doesnt exist";
            }
          } while ($edit_pick != 0);
        }
      } elseif (!ctype_digit("$visitor_id")) {
        $i = 0;
        echo "Invalid visitor ID";
      }

      if (intval($visitor_id) == 0) {
        $d = 1;
      }
    } while ($d == 0);

    break;

  case 3:
    echo "Delete ";
    $string = readline('Enter visitors ID\'s name (ex.: 1 2 5 6): ');
    $regex = '~(?<!\\\\)".*?(?<!\\\\)"(*SKIP)(*FAIL)|\ ~';

    $z = [];
    $arguments = preg_split($regex, $string);
    echo " We will remove from array: ";
    foreach ($arguments as $line) {
      if (ctype_digit("$line")) {
        echo $line . "; ";
        array_push($z, intval($line));
      }
    }
    echo "\n\r";

    if (count($z) > 0) {
      $valid = (int)readline("Are you sure want to remove theese elements? The Changes will be saved. (0-No; 1-Yes) \n\r");
      if ($valid == 1) {
        foreach ($z as $line) {
          echo $line . " ";
          unset($csv[$line - 1]);
        }
        $csv = array_values($csv);

        $fout = fopen($file, 'w');
        foreach ($csv as $line) {
          fputcsv($fout, $line);
        }
        fclose($fout);
      }
    }
    break;
}


?>