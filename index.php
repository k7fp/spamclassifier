<?
//config variables, details explained above
$nonspamdir		= "spamnot";
$spamdir		= "spam";
$vocabfilename	= "vocabs.txt";
$thetafilename	= "theta.dat";

//the string/post/email message you want to determine if it's spam or not
$xtest = ($_REQUEST['data'] ? $_REQUEST['data'] : "The test data to classify");

//don't need to change anything after this point except how to deal with the output around line 194, $result = 0 means not spam, otherwise it's spam
//vocabulary file needs to exist so we can get the needed data, otherwise quit
if(file_exists($vocabfilename) && filesize($vocabfilename) != 0) {
	$handle = fopen($vocabfilename, 'r');
	$vocabfile = fread($handle, filesize($vocabfilename));
	fclose($handle);
} else
	throwerror("A vocabulary file containing comma delimited words is needed.");
	
//convert all words in the spam string to lower case, then convert it to unique array
$spamarray = array_unique(explode(",",strtolower($vocabfile)));
//sort the spam array
sort($spamarray);

//check if theta already exists, if it does, we do not need to train
if(file_exists($thetafilename) && filesize($thetafilename) != 0) {
	//theta exists, so just read it off file
	$handle = fopen($thetafilename, 'r');
	$theta = unserialize(fread($handle, filesize($thetafilename)));
	fclose($handle);
} else {
	//theta doesn't exist, so we train it and then save theta
	//$ytrain[$key] = 0 if not spam, 1 if spam
	//the directory for nonspam email files, can be no or any extension, just make sure 1 file contains 1 sample for the nonspam data
	$handle = opendir("$nonspamdir/");
	$counter = 0;
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
				$xtrain[$counter] = implode('', file("$nonspamdir/$file"));
				$ytrain[$counter] = 0;
				$counter++;
		}
	}
	//do the same thing for the spam data for training, define spam data directory
	$handle = opendir("$spamdir/");
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
				$xtrain[$counter] = implode('', file("$spamdir/$file"));
				$ytrain[$counter] = 1;
				$counter++;
		}
	}
	
	//initialize spamcounts, theta, log(theta), and log(1-theta) arrays with zero sparse matricies
	//this is assuming there are 2 cases, nonspams and spams, if more than 2 cases, change the max $i value
	foreach($spamarray as $key => $value) {
		for($i = 0; $i < 2; $i++) {
			$spamcounts[$i][$key] = 0;
			$theta[$key][$i] = 0;
			$logtheta[$key][$i] = 0;
			$logoneminustheta[$key][$i] = 0;
		}
	}
	
	//get the counts of a word appearing in each class
	foreach($xtrain as $xkey => $traindata) {
		foreach(detectedArray(strtolower($traindata)) as $key => $value) {
			$spamcounts[$ytrain[$xkey]][$key]++;
		}
	}
	
	//get the total number of spam and nonspam training cases
	$ycases = array_count_values($ytrain);
	
	//get the posterior mean estimate using thetajc = Njc+1/Nc+2 where j is a word and c is a class
	foreach($spamcounts as $key => $value) {
		foreach($value as $njc => $njcvalue) {
			$theta[$njc][$key] = ($njcvalue+1) / ($ycases[$key] + 2);
		}
	}
	
	//save theta to file so we don't have to train it again to save time
	$handle = fopen($thetafilename, 'w');
	fwrite($handle, serialize($theta));
	fclose($handle);
}

/*--this is the part where we actually calculate if the data is spam or not, using the training data we got above or from the file--*/

//testing string shouldn't be empty
if($xtest == "")
	throwerror("The data string to check whether spam or not is missing.");

//use counts * log(theta) + (1-counts)*logs(1-theta), take max index
//assume uniform class prior, then the posterior is just the log-likelihood
//first thing get log(theta) and log(1-theta), so we need the theta we got from training
foreach($theta as $tkey => $tvalue) {
	foreach($tvalue as $key => $value) {
		$logtheta[$tkey][$key] = log($theta[$tkey][$key]);
		$logoneminustheta[$tkey][$key] = log(1-$theta[$tkey][$key]);
	}
}
//initialize an array to get whether a word in the vocabulary appears in this document
foreach($spamarray as $key => $value) {
	$thiscount[$key] = 0;
}
//process the testing document
foreach(detectedArray(strtolower($xtest)) as $key => $value) {
	$thiscount[$key]++;
}
//flip the $thiscount array to get 1-counts
foreach($thiscount as $key => $value)
	$oneminusthiscount[$key] = 1 - $thiscount[$key];

//get counts * log(theta) first
foreach($logtheta as $lkey => $lvalue) {
	foreach($lvalue as $key => $value) {
		$sum[$key] = $sum[$key] + ($thiscount[$lkey] * $value);	
	}
}
//then get (1-counts)*logs(1-theta), add onto $sum on the go
foreach($logoneminustheta as $lkey => $lvalue) {
	foreach($lvalue as $key => $value) {
		$sum[$key] = $sum[$key] + ($oneminusthiscount[$lkey] * $value);	
	}
}
//get the maximum index at of the array, define max as max allowed number
$result = implode("",array_keys($sum, max($sum)));

//whatever you want to do with the result is completely up to you, here it prints if it's spam or not spam, notice nonspam reuslt has value of 0
printf("<font color='blue'>Now classifyling the below data:</font><br><br>%s<br><br>",$xtest);
if($result == 0)
	print "<font color='blue'>Result: </font><strong><font color='green'>NOT SPAM</font></strong>";
else
	print "<font color='blue'>Result: </font><strong><font color='red'>SPAM</font></strong>";





//stripping punctuation of the string then put them into an array, and return the words that are in the spam array
function detectedArray($data) {
	global $spamarray;
	$data = preg_replace('/\W/', ' ', $data);
	$data = array_unique(explode(" ",$data));
	return array_intersect($spamarray, $data);
}

//function to print the error message in red, and then kill the program
function throwerror($string) {
	print"<strong><font color='red'>Error: $string</font></strong>";
	die;
}
?>