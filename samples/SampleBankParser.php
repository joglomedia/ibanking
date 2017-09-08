<?php
/**
 * This file is part of the IBanking library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
error_reporting(E_ALL);

//include('vendor/autoload.php');
include('../src/IBanking/Utils/HttpHelper.php');
include('../src/IBanking/Utils/HttpRequest.php');
include('../src/IBanking/Utils/HtmlParser.php');
include('../src/IBanking/Utils/Function.php');
include('../src/IBanking/IBParser/IBParserInterface.php');
include('../src/IBanking/IBParser/AbstractIBParser.php');
include('../src/IBanking/IBParser/SampleBankParser.php');
include('../src/IBanking/IBanking.php');

use IBanking\IBanking as IBanking;
use IBanking\IBParser\SampleBankParser as SBParser;

$credentials = [
    'corpid'   => '',
    'username' => '', 
    'password' => '',
    'account'  => '',
];

$ibank = new IBanking(new SBParser, $credentials);

$loggedin = $ibank->login();

var_dump($loggedin);
echo("\r\n");

$balance = $ibank->getBalance();
var_dump($balance);
echo("\r\n");

$mutasi = $ibank->getStatements('24/7/2017', '29/7/2017', 'credit');
var_dump($mutasi);
echo("\r\n");

var_dump($ibank->isLoggedin($session = true));

$ibank->logout();
