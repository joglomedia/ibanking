<?php
/**
 * This file is part of the IBank library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
error_reporting(E_ALL);

//include('vendor/autoload.php');
include('../src/IBank/Utils/HttpHelper.php');
include('../src/IBank/Utils/HttpRequest.php');
include('../src/IBank/Utils/HtmlParser.php');
include('../src/IBank/Utils/Functions.php');
include('../src/IBank/IBParser/IBParserInterface.php');
include('../src/IBank/IBParser/AbstractIBParser.php');
include('../src/IBank/IBParser/SampleBankParser.php');
include('../src/IBank/IBank.php');

use IBank\IBank as IBank;
use IBank\IBParser\SampleBankParser as SBParser;

$credentials = [
	'corpid'	=> '',
	'username'	=> '', 
	'password'	=> '',
	'account'	=> '',
];

$ibank = new IBank(new SBParser, $credentials);

$loggedin = $ibank->login();

var_dump($loggedin);
echo("\r\n");

$balance = $ibank->getBalance();
var_dump($balance);
echo("\r\n");

$mutasi = $ibank->getTransactions('24/7/2017', '29/7/2017', 'credit');
var_dump($mutasi);
echo("\r\n");

var_dump($ibank->isLoggedin($session=true));

$ibank->logout();
