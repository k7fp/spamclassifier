What is this program:
 - The program uses Naïve Bayes text classifier to determine whether a given string of message (data) is a spam based on the vocabulary and training set you provide. Because it's based on stuff you provide, you get to train the spam filter to work better for the data that is suitable for you. Nothing is perfect, neither is this program, there will be small errors and people who know what they are doing getting around the spam filter. It is probably not a good idea to use this program for email filtering (not that you can't) because there are already many default spam filters within the email programs; this is more intended for websites that have blogs, comments, etc.
 - For more information on Naïve Bayes Classifer, check out http://en.wikipedia.org/wiki/Naive_bayes_classifier

What you'll need:
 - Examples of nonspam data (such as legitimate emails, regular comments on your website, etc) in separate files each, all in a single directory.
 - Examples of spam data (spam emails, comment box spams on your websites, etc) in separate files each, all in a single directory.
 - A list of vocabularies in a comma delimited file, it can contain everyday words, spam sensitive words, phrases. The way you choose the vocabularie is important because only words that are defined in the vocabularies file will be used to determine if the data is spam.
 - Abiliity to CHMOD this file 777 or it cannot write the posterior mean estimate of the training data to a file, if your data/vocabs are large, it will take quite some time to process, therefore it's good to save the array to a file and just fetch it whenever we need to use it since classifyin the data does not require changing theta. You can simply delete that file whenever you want to redefine your vocabs or have new sets of training data. 

Recommended:
 - Vocabulary list should contain a list of words and/or phrases that are related to whatever you want to use it for, as well as some common words which you encounter everyday such as 'the', 'if', 'you', 'our', etc.
 - Number of spam and nonspam samples should be the same or roughly the same amount.
 - Have many vocabulary and spam / nonspam data.
 - Suggested number of vocabularies: At least 10000.
 - Suggested number of sample spam data: 1500 (of course you might not have that many training data, but if you do, it's always good to give it more).
 - Suggested MINIMUM number of sample spam data: 50 (might not be good enough depends on your vocabulary list, but it's pretty subjective).
 - Suggested number and minimum number of sample nonspam data: same as for spam data.

** I've only included very few sample words in the vocabulary and only 1 example (they can be in any extension) for spam/nonspam, so you can't just run this program as is with my default files and expect a good result with classifying your test data.

explaination of the config variables below:
$nonspamdir		// the name of the directory where you put the nonspam training data.
$spamdir 		// the name of the directory for the spam messages.
$vocabfilename	// the name of the vocabulary file, in the same folder as this file.
$thetafilename	// file containg the posterior mean estimate for the training data.