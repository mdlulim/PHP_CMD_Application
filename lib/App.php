<?php

namespace Minicli;

class App
{
    protected $printer;

    public function __construct()
    {
        $this->printer = new CliPrinter();
    }

    public function getPrinter()
    {
        return $this->printer;
    }

    function getStudentById($array, $student_id){
        $flag = false;
        foreach ($array as $student){
            if ($student['student_id'] === $student_id){
                $flag = true;
            }
        }
        return $flag;
    }


    public function runCommand($argv)
    {
        $myFile = "students/data.json";
        $arr_data = array(); // create empty array

        $name = "World";
        if (isset($argv[1])) {
            if($argv[1] == "list"){
                $this->displayStudents();
                exit(0);
            }else if($argv[1] == "delete" ){
                $student_id =$argv[2];
                $this->deleteStudent($student_id);
                exit(0);
            } else if($argv[1] == "update" ){
                $student_id =$argv[2];
                $this->updateStudent($student_id);
                exit(0);
            }
        }else{

            fwrite(STDOUT, "Please enter Student Id: ");
            $id = fgets(STDIN);
            fwrite(STDOUT, "Please enter name: ");
            $name = fgets(STDIN);
            fwrite(STDOUT, "Please enter last name: ");
            $lastname = fgets(STDIN);
            fwrite(STDOUT, "Please enter  Age: ");
            $age = fgets(STDIN);
            fwrite(STDOUT, "Please enter  curriculum: ");
            $curriculum = fgets(STDIN);
            
            try{
                $jsondata = file_get_contents($myFile);
                $arr_data = json_decode($jsondata, true);

                $student_id = str_replace("\n","",$id);
                
                $flag = $this->getStudentById($arr_data, $student_id);
                // Validate fields
                
                if(strlen($student_id) != 7){
                    echo '========================== Error ========================== ',"\n";
                    $this->getPrinter()->display("Invalid student number. must be consist of 7 digits.");
                    echo '============================================================= \n',"\n";
                    fwrite(STDOUT, "Please enter Student Id: ");
                    $id = fgets(STDIN);
                }

                if($flag == true){
                    echo '========================== Error ========================== ',"\n";
                    $this->getPrinter()->display("Student number is already exist");
                    echo '============================================================= ',"\n"; 
                }else{
                    $student_id = str_replace("\n","",$id); 
                    $name = str_replace("\n","",$name); 
                    $lastname = str_replace("\n","",$lastname);
                    $age = str_replace("\n","",$age);
                    $curriculum = str_replace("\n","",$curriculum);

                    if(empty($student_id) || empty($name) || empty($lastname) || empty($age) || empty($curriculum)){
                        $this->getPrinter()->display("All field are mandatory!!");
                        echo '============================================================= ',"\n"; 
                    }else{
                        $data = array(
                            "student_id"=> $student_id, 
                            "name"=> $name, 
                            "lastname"=> $lastname,
                            "age"=> $age, 
                            "curriculum"=> $curriculum);

                        array_push($arr_data,$data);
                        $jsondata = json_encode($arr_data, JSON_PRETTY_PRINT);
                        
                        if(file_put_contents($myFile, $jsondata)) {
                            echo '========================== Success ========================== \n',"\n";
                            $this->getPrinter()->display("Student was successfully added");
                            echo '============================================================= \n',"\n";
                        }else{
                            echo "error","\n";
                        }
                    }
                        
                    // var_dump($character);
                    // fwrite(STDOUT, "Hello ".$character->name);
                    exit(0);
                }
                

            }catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
    }

    public function displayStudents(){
        $myFile = "students/data.json";
        $arr_data = array(); // create empty array

        try{
            $jsondata = file_get_contents($myFile);
            $arr_data = json_decode($jsondata, true);

            // fixed width
            $mask = "|%7.5s |%-10.30s |%-10.30s |%7.5s |%-20.30s |\n";
            printf($mask, 'StudentNo', 'Name', 'Surname', 'Age', 'Curriculum');
            foreach ($arr_data as $student){
                printf($mask, $student['student_id'], $student['name'], $student['lastname'], $student['age'], $student['curriculum']);  
            }
        }catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }    

    }

    public function deleteStudent($student_id){
        
        $myFile = "students/data.json";
        $arr_data = array(); // create empty array

        try{
            $jsondata = file_get_contents($myFile);
            $arr_data = json_decode($jsondata, true);

            $count = 0;
            $target = null;

            foreach ($arr_data as $student){
                if ($student['student_id'] === $student_id){
                    $target = $count;
                    break;
                }
                $count = $count + 1;
            }
            if($target != null){
                unset($arr_data[$target]);
                $jsondata = json_encode($arr_data, JSON_PRETTY_PRINT);
                    
                    if(file_put_contents($myFile, $jsondata)) {
                        echo '========================== Delete Student ========================== \n',"\n";
                        $this->getPrinter()->display("Student was successfully deleted");
                        echo '============================================================= \n',"\n";
                    }else{
                        echo "error","\n";
                    }
            }
            
        }catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }    

    }

    public function updateStudent($student_id){
        echo '========================== Update Student ========================== \n',"\n";
        $myFile = "students/data.json";
        $arr_data = array(); // create empty array

        try{
            $jsondata = file_get_contents($myFile);
            $arr_data = json_decode($jsondata, true);

            $count = 1;
            $target = null;

            $name = null; $lastname = null; $age=null; $curriculum= null;

            foreach ($arr_data as $student){
                if ($student['student_id'] === $student_id){
                    $name = $student['name'];
                    $lastname = $student['lastname'];
                    $age=$student['age'];
                    $curriculum= $student['curriculum'];
                    $target = $count;
                    break;
                }
                $count = $count + 1;
            }
            if($target){
                
                fwrite(STDOUT, "Enter name [".$name."] : ");
                $name2 = fgets(STDIN);
                fwrite(STDOUT, "Enter last name [".$lastname."] : ");
                $lastname2 = fgets(STDIN);
                fwrite(STDOUT, "Enter  Age[".$age."] : ");
                $age2 = fgets(STDIN);
                fwrite(STDOUT, "Enter  curriculum[".$curriculum."] : ");
                $curriculum2 = fgets(STDIN);

                $name2 = str_replace("\n","",$name2); 
                $lastname2 = str_replace("\n","",$lastname2);
                $age2 = str_replace("\n","",$age2);
                $curriculum2 = str_replace("\n","",$curriculum2);

                if(empty($name2) || empty($lastname2) || empty($age2) || empty($curriculum2)){
                    $this->getPrinter()->display("All field are mandatory!!");
                    echo '============================================================= ',"\n"; 
                }else{
                    $arr_data[$target - 1]['name'] = $name2;
                    $arr_data[$target - 1]['lastname'] = $lastname2;
                    $arr_data[$target - 1]['age'] = $age2;
                    $arr_data[$target - 1]['curriculum'] = $curriculum2;

                    $jsondata = json_encode($arr_data, JSON_PRETTY_PRINT);
                        
                    if(file_put_contents($myFile, $jsondata)) {
                        $this->getPrinter()->display("Student was successfully updated");
                        echo '============================================================= \n',"\n";
                    }else{
                        echo "error","\n";
                    }
                }
            }
            
        }catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }    

    }
}