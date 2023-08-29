
<?php
class Person {

    private $ID;
    private $first_name;
    private $last_name;
    private $gender;
    private $birthdate;

    public function __construct($ID, $first_name, $last_name, $gender, $birthdate) {
        $this->ID = $ID;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->gender = $gender;
        $this->birthdate = $birthdate;
    }

    public function getID() {
        return $this->ID;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getGender() {
        return $this->gender;
    }

    public function getBirthdate() {
        return $this->birthdate;
    }

    public function ageInDays() {
        $today = new DateTime();
        $birthdate = new DateTime($this->birthdate);
        $age = $birthdate->diff($today);
        return $age->days;
    }
}

class Group {

    private static $instance = null;
    public $people = [];

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Group();
        }
        return self::$instance;
    }

    public function addPerson($person) {
        $this->people[$person->getID()] = $person;
    }

    public function getPerson($person_id) {
        return isset($this->people[$person_id]) ? $this->people[$person_id] : null;
    }

    public function genderPercentage($gender) {
        $totalPeople = count($this->people);
        $genderCount = 0;
    
        foreach ($this->people as $person) {
            if ($person->getGender() === $gender) {
                $genderCount++;
            }
        }
    
        if ($totalPeople > 0) {
            $percentage = ($genderCount / $totalPeople) * 100;
        } else {
            $percentage = 0;
        }
    
        return $percentage;
    }
}

function loadData($file_path) {
    $group = Group::getInstance();

    $handle = fopen($file_path, 'r');

    if ($handle) {
        $personData = [];

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if ($line === "") {
                if (!empty($personData)) {
                    list($name, $genderAndBirthdate) = $personData;
                    list($gender, $birthdate) = explode(' ', $genderAndBirthdate);
                    $birthdate = DateTime::createFromFormat('d.m.Y', $birthdate)->format('Y-m-d');
                    
                    $nameParts = explode(' ', $name);
                    $first_name = $nameParts[0];
                    $last_name = isset($nameParts[1]) ? $nameParts[1] : "";
                    
                    $person = new Person(count($group->people) + 1, $first_name, $last_name, $gender, $birthdate);
                    $group->addPerson($person);
                    
                    $personData = [];
                }
            } else {
                $personData[] = $line;
            }
        }

        if (!empty($personData)) {
            list($name, $genderAndBirthdate) = $personData;
            list($gender, $birthdate) = explode(' ', $genderAndBirthdate);
            $birthdate = DateTime::createFromFormat('d.m.Y', $birthdate)->format('Y-m-d');
            
            $nameParts = explode(' ', $name);
            $first_name = $nameParts[0];
            $last_name = isset($nameParts[1]) ? $nameParts[1] : "";
            
            $person = new Person(count($group->people) + 1, $first_name, $last_name, $gender, $birthdate);
            $group->addPerson($person);
        }
        
        fclose($handle);
    } else {
        echo "Nepodařilo se otevřít soubor pro čtení.";
    }

    return $group;
}

$file_path = 'data.txt';
$group = loadData($file_path);

foreach ($group->people as $person) {
    echo "ID: {$person->getID()}, Jméno: {$person->getFirstName()}, Příjmení: {$person->getLastName()}, Věk: {$person->ageInDays()} dnů, Pohlaví : {$person->getGender()}<br>";
}

$malePercentage = $group->genderPercentage('muž');
$femalePercentage = $group->genderPercentage('žena');


echo "Podíl mužů: " . number_format($malePercentage, 2) . "%\n";
echo "Podíl žen: " . number_format($femalePercentage, 2) . "%\n";




