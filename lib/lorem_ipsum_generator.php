<?php
	/*
	 * Mathew Tinsley's lorem ipsum generator
	 * PHP version with helpers
	 *
	 * Warning:
	 *  this library uses LF (UNIX) as the end-of-line character
	 *
	 * Functions:
		generate_lorem_ipsum_wp(
			int_words,
			int_paragraphs,
			bool_start_with_lipsum=true, // Lorem ipsum dolor sit amet consectetuer adipiscing
			bool_double_newline=false, // add \n\n after each line
			string_start_tag='',
			string_end_tag=''
		)
		generate_lorem_ipsum_b(
			int_bytes,
			string_start_tag='',
			string_end_tag=''
		)
	 * Usage:
		$content=generate_lorem_ipsum_wp(30, 4);
		$content=generate_lorem_ipsum_wp(30, 4, true, true, '<p>', '</p>');
		$content=generate_lorem_ipsum_b(400);
		$content=generate_lorem_ipsum_b(400, '<p>', '</p>');
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

	function generate_lorem_ipsum_wp(
		int $words,
		int $paragraphs,
		bool $start_with_lipsum=true,
		bool $double_newline=false,
		string $start_tag='',
		string $end_tag=''
	){
		$lipsum=new mt_lorem_ipsum();
		$output='';

		if($start_with_lipsum)
		{
			$words_patch=0;

			if($words > 7)
			{
				$words_patch=7;
				$output.=$start_tag.'Lorem ipsum dolor sit amet consectetuer adipiscing ';
			}
			else if($words > 5)
			{
				$words_patch=5;
				$output.=$start_tag.'Lorem ipsum dolor sit amet ';
			}
			else if($words > 2)
			{
				$words_patch=2;
				$output.=$start_tag.'Lorem ipsum ';
			}
		}

		for($i=1; $i<=$paragraphs; ++$i)
		{
			if($start_with_lipsum && ($i === 1))
			{
				$patch=$lipsum->generate($words-$words_patch);
				$output.=strtolower(substr($patch, 0, 1)).substr($patch, 1).$end_tag;
			}
			else
				$output.=$start_tag.$lipsum->generate($words).$end_tag;

			if($i < $paragraphs)
			{
				if($double_newline)
					$output.="\n\n";
				else
					$output.="\n";
			}
		}

		return $output;
	}
	function generate_lorem_ipsum_b(
		int $bytes,
		string $start_tag='',
		string $end_tag=''
	){
		$lipsum=new mt_lorem_ipsum();
		$output='';

		while(strlen($output) < $bytes)
		{
			if(strlen($output) > 0)
				$output.=' ';

			$output.=$lipsum->generate(10);
		}

		switch(substr($output, $bytes-2, $bytes-1))
		{
			case ' ':
			case '.':
			case ',':
				return $start_tag.substr($output, 0, $bytes-2).'a.'.$end_tag;
		}

		return $start_tag.substr($output, 0, $bytes-1).'.'.$end_tag;
	}

	class mt_lorem_ipsum
	{
		protected static $words=[
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

		protected $words_per_sentence_avg=24.460;
		protected $words_per_sentence_std=5.080;

		protected function _js_rand()
		{
			return (float)rand()/(float)getrandmax();
		}
		protected function gauss()
		{
			return ($this->_js_rand()*2-1)
			+	($this->_js_rand()*2-1)
			+	($this->_js_rand()*2-1);
		}
		protected function gauss_ms($mean, $standard_deviation)
		{
			return round($this->gauss()*$standard_deviation+$mean);
		}
		protected function get_random_comma_count($word_length)
		{
			$base=6;
			$average=log($word_length)/log($base);
			$standard_deviation=$average/$base;

			return round($this->gauss_ms($average, $standard_deviation));
		}
		protected function get_random_sentence_length()
		{
			return round($this->gauss_ms(
				$this->words_per_sentence_avg,
				$this->words_per_sentence_std
			));
		}
		protected function punctuate($sentence)
		{
			$word_length=count($sentence);
			$sentence[$word_length-1].='.';

			if($word_length < 4)
				return $sentence;

			$num_commas=$this->get_random_comma_count($word_length);

			for($ii=0; $ii<=$num_commas; ++$ii)
			{
				$position=round($ii*$word_length/($num_commas+1));

				if(($position < ($word_length-1)) && ($position > 0))
					$sentence[$position].=',';
			}

			$sentence[0]=strtoupper(substr($sentence[0], 0, 1)).substr($sentence[0], 1);

			return $sentence;
		}

		public function generate(int $num_words=100)
		{
			for($ii=0; $ii<$num_words; ++$ii)
			{
				$position=rand(0, count(static::$words)-1);
				$word=static::$words[$position];

				if(($ii > 0) && ($words[$ii-1] === $word))
					$ii-=1;
				else
					$words[$ii]=$word;
			}

			$sentences=[];
			$current=0;

			while($num_words > 0)
			{
				$sentence_length=$this->get_random_sentence_length();

				if(($num_words-$sentence_length) < 4)
					$sentence_length=$num_words;

				$num_words-=$sentence_length;
				$sentence=[];

				for($ii=$current; $ii<($current+$sentence_length); ++$ii)
					$sentence[]=$words[$ii];

				$sentence=$this->punctuate($sentence);
				$current+=$sentence_length;
				$sentences[]=implode(' ', $sentence);
			}

			return implode(' ', $sentences);
		}
	}
?>