#!/usr/bin/php
<?php

define("FOLDER_ROOT", dirname(__FILE__).DIRECTORY_SEPARATOR);

class Cartella {
    public $root;
    public $address;

    public function __construct($root, $address) {
        $this->root = $root;
        $this->address = $address;
    }

    public function write() {
        // Scrivila se non esiste
        if(!file_exists($this->root.$this->address)) {
            if(mkdir($this->root.$this->address, 0777, true)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function erase() {
        // Cancellala solo se vuota
        if(file_exists($this->root.$this->address)) {
            //unlink('homestead.bak');
        }
        return true;
    }
}

class Cartelle {
    public $listText;
    public $list;

    public function __construct() {
        $this->listText = [];
        $this->list = [];
    }

    public function AddText($cartella) {
        $this->listText[] = str_replace(array("\r\n", "\n", "\r"), "", $cartella);;
    }

    public function Add($cartella) {
        $this->list[] = $cartella;
    }

    public function Tab($text) {
        return substr_count($text, "\t");
    }

    public function Analyse() {
        $levelMother = [];
        $numberTab = 0;
        $numberTabLast = 0;
        
        for($c = 0; $c < count($this->listText); $c++) {
            
            $numberTab = $this->Tab($this->listText[$c]);
            $dirText = str_replace("\t", "", $this->listText[$c]);
            
            if($numberTab == 0) {
                unset($levelMother);
                $levelMother = [];
                array_push($levelMother, $dirText);
            }

            if(($numberTab < $numberTabLast) && ($numberTab<>0)) {
                $diff = abs($numberTab - $numberTabLast);
                for($x = 0; $x <= ($diff); $x++) {
                    array_pop($levelMother);
                }
                array_push($levelMother, $dirText);
            }
            
            if(($numberTab > $numberTabLast) && ($numberTab<>0)) {
                array_push($levelMother, $dirText);
            }

            if(($numberTab == $numberTabLast) && ($numberTab<>0)) {
                array_pop($levelMother);
                array_push($levelMother, $dirText);
            }

            if(count($levelMother)>0) {
                $address = "";
                foreach($levelMother as $dir) {
                    $address .= $dir.DIRECTORY_SEPARATOR;
                }
                $this->Add(new Cartella(FOLDER_ROOT, $address));
            }            
            $numberTabLast = $numberTab;
        }
    }

    public function Visualizza() {
        foreach($this->list as $dir) {
            echo $dir->root.$dir->address."\n";
        }
    }

    public function Create() {
        foreach($this->list as $dir) {
            if($dir->write()) {
                echo "Create: ".$dir->root.$dir->address."\n";
            } else {
                echo "Failed create: ".$dir->root.$dir->address."\n";
            }
        }
    }
}

$cartelle = new Cartelle();

$listFile = fopen("list.txt", "r");
if ($listFile) {
    while (($line = fgets($listFile)) !== false) {
        $cartelle->AddText($line); 
    }
    fclose($listFile);
} else {
    // error opening the file.
    echo "Errore nella lettura del file list.txt";
}

$cartelle->Analyse();
//$cartelle->Visualizza();
$cartelle->Create();

?>