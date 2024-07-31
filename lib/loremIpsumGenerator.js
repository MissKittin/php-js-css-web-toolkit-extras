/*
 * Mathew Tinsley's lorem ipsum generator
 * with helpers
 *
 * Functions:
	generateLoremIpsumWP(
		int_words,
		int_paragraphs,
		bool_startWithLipsum=true, // Lorem ipsum dolor sit amet consectetuer adipiscing
		bool_doubleNewline=false, // add \n\n after each line
		string_startTag='',
		string_endTag=''
	)
	generateLoremIpsumB(
		int_bytes,
		string_startTag='',
		string_endTag=''
	)
 * Usage:
	content=generateLoremIpsumWP(30, 4);
	content=generateLoremIpsumWP(30, 4, true, true, '<p>', '</p>');
	content=generateLoremIpsumB(400);
	content=generateLoremIpsumB(400, '<p>', '</p>');
 *
 * Source: https://gist.github.com/rviscomi/1479649
 * License: 3-Clause BSD
 */

/*
 *	Copyright (c) 2009, Mathew Tinsley (tinsley@tinsology.net)
 *	All rights reserved.
 *
 *	Redistribution and use in source and binary forms, with or without
 *	modification, are permitted provided that the following conditions are met:
 *		* Redistributions of source code must retain the above copyright
 *		  notice, this list of conditions and the following disclaimer.
 *		* Redistributions in binary form must reproduce the above copyright
 *		  notice, this list of conditions and the following disclaimer in the
 *		  documentation and/or other materials provided with the distribution.
 *		* Neither the name of the organization nor the
 *		  names of its contributors may be used to endorse or promote products
 *		  derived from this software without specific prior written permission.
 *
 *	THIS SOFTWARE IS PROVIDED BY MATHEW TINSLEY ''AS IS'' AND ANY
 *	EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *	DISCLAIMED. IN NO EVENT SHALL <copyright holder> BE LIABLE FOR ANY
 *	DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

function generateLoremIpsumWP(
	words,
	paragraphs,
	startWithLipsum=true,
	doubleNewline=false,
	startTag='',
	endTag=''
){
	var lipsum=new MtLoremIpsum();
	var output='';

	if(startWithLipsum)
	{
		var wordsPatch=0;

		if(words > 7)
		{
			wordsPatch=7;
			output+=startTag+'Lorem ipsum dolor sit amet consectetuer adipiscing ';
		}
		else if(words > 5)
		{
			wordsPatch=5;
			output+=startTag+'Lorem ipsum dolor sit amet ';
		}
		else if(words > 2)
		{
			wordsPatch=2;
			output+=startTag+'Lorem ipsum ';
		}
	}

	for(var i=1; i<=paragraphs; i++)
	{
		if(startWithLipsum && (i === 1))
		{
			var patch=lipsum.generate(words-wordsPatch);
			output+=patch.charAt(0).toLowerCase()+patch.slice(1)+endTag;
		}
		else
			output+=startTag+lipsum.generate(words)+endTag;

		if(i < paragraphs)
		{
			if(doubleNewline)
				output+="\n\n";
			else
				output+="\n";
		}
	}

	return output;
}
function generateLoremIpsumB(bytes, startTag='', endTag='')
{
	var lipsum=new MtLoremIpsum();
	var output='';

	while(output.length < bytes)
	{
		if(output.length > 0)
			output+=' ';

		output+=lipsum.generate(10);
	}

	switch(output.substring(bytes-2, bytes-1))
	{
		case ' ':
		case '.':
		case ',':
			return startTag+output.substring(0, bytes-2)+'a.'+endTag;
	}

	return startTag+output.substring(0, bytes-1)+'.'+endTag;
}

/* class MtLoremIpsum */
/* { */
	var MtLoremIpsum=function(){};

	MtLoremIpsum.WORDS_PER_SENTENCE_AVG=24.460;
	MtLoremIpsum.WORDS_PER_SENTENCE_STD=5.080;
	MtLoremIpsum.WORDS=[
		'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur',
		'adipiscing', 'elit', 'curabitur', 'vel', 'hendrerit', 'libero',
		'eleifend', 'blandit', 'nunc', 'ornare', 'odio', 'ut',
		'orci', 'gravida', 'imperdiet', 'nullam', 'purus', 'lacinia',
		'a', 'pretium', 'quis', 'congue', 'praesent', 'sagittis',
		'laoreet', 'auctor', 'mauris', 'non', 'velit', 'eros',
		'dictum', 'proin', 'accumsan', 'sapien', 'nec', 'massa',
		'volutpat', 'venenatis', 'sed', 'eu', 'molestie', 'lacus',
		'quisque', 'porttitor', 'ligula', 'dui', 'mollis', 'tempus',
		'at', 'magna', 'vestibulum', 'turpis', 'ac', 'diam',
		'tincidunt', 'id', 'condimentum', 'enim', 'sodales', 'in',
		'hac', 'habitasse', 'platea', 'dictumst', 'aenean', 'neque',
		'fusce', 'augue', 'leo', 'eget', 'semper', 'mattis',
		'tortor', 'scelerisque', 'nulla', 'interdum', 'tellus', 'malesuada',
		'rhoncus', 'porta', 'sem', 'aliquet', 'et', 'nam',
		'suspendisse', 'potenti', 'vivamus', 'luctus', 'fringilla', 'erat',
		'donec', 'justo', 'vehicula', 'ultricies', 'varius', 'ante',
		'primis', 'faucibus', 'ultrices', 'posuere', 'cubilia', 'curae',
		'etiam', 'cursus', 'aliquam', 'quam', 'dapibus', 'nisl',
		'feugiat', 'egestas', 'class', 'aptent', 'taciti', 'sociosqu',
		'ad', 'litora', 'torquent', 'per', 'conubia', 'nostra',
		'inceptos', 'himenaeos', 'phasellus', 'nibh', 'pulvinar', 'vitae',
		'urna', 'iaculis', 'lobortis', 'nisi', 'viverra', 'arcu',
		'morbi', 'pellentesque', 'metus', 'commodo', 'ut', 'facilisis',
		'felis', 'tristique', 'ullamcorper', 'placerat', 'aenean', 'convallis',
		'sollicitudin', 'integer', 'rutrum', 'duis', 'est', 'etiam',
		'bibendum', 'donec', 'pharetra', 'vulputate', 'maecenas', 'mi',
		'fermentum', 'consequat', 'suscipit', 'aliquam', 'habitant', 'senectus',
		'netus', 'fames', 'quisque', 'euismod', 'curabitur', 'lectus',
		'elementum', 'tempor', 'risus', 'cras'
	];

	MtLoremIpsum.prototype.generate=function(numWords=100)
	{
		var words=[], ii, position, word, current, sentences, sentenceLength, sentence;

		for(ii=0; ii<numWords; ii++)
		{
			position=Math.floor(Math.random()*MtLoremIpsum.WORDS.length);
			word=MtLoremIpsum.WORDS[position];

			if((ii > 0) && (words[ii-1] === word))
				ii-=1;
			else
				words[ii]=word;
		}

		sentences=[];
		current=0;

		while(numWords > 0)
		{
			sentenceLength=this.getRandomSentenceLength();

			if((numWords-sentenceLength) < 4)
				sentenceLength=numWords;

			numWords-=sentenceLength;
			sentence=[];

			for(ii=current; ii<(current+sentenceLength); ii++)
				sentence.push(words[ii]);

			sentence=this.punctuate(sentence);
			current+=sentenceLength;
			sentences.push(sentence.join(' '));
		}

		return sentences.join(' ');
	};
	MtLoremIpsum.prototype.punctuate=function(sentence)
	{
		var wordLength, numCommas, ii, position;
		wordLength=sentence.length;
		sentence[wordLength-1]+='.';

		if(wordLength < 4)
			return sentence;

		numCommas=this.getRandomCommaCount(wordLength);

		for(ii=0; ii<=numCommas; ii++)
		{
			position=Math.round(ii*wordLength/(numCommas+1));

			if((position < (wordLength-1)) && (position > 0))
				sentence[position]+=',';
		}

		sentence[0]=sentence[0].charAt(0).toUpperCase()+sentence[0].slice(1);

		return sentence;
	};
	MtLoremIpsum.prototype.getRandomCommaCount=function(wordLength)
	{
		var base, average, standardDeviation;
		base=6;
		average=Math.log(wordLength)/Math.log(base);
		standardDeviation=average/base;

		return Math.round(this.gaussMS(average, standardDeviation));
	};
	MtLoremIpsum.prototype.getRandomSentenceLength=function()
	{
		return Math.round(this.gaussMS(
			MtLoremIpsum.WORDS_PER_SENTENCE_AVG,
			MtLoremIpsum.WORDS_PER_SENTENCE_STD
		));
	};
	MtLoremIpsum.prototype.gauss=function()
	{
		return (Math.random()*2-1)+(Math.random()*2-1)+(Math.random()*2-1);
	};
	MtLoremIpsum.prototype.gaussMS=function(mean, standardDeviation)
	{
		return Math.round(this.gauss()*standardDeviation+mean);
	};
/* } */