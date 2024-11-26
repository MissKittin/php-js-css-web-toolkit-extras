function melinskrypt(element, words=[
	'elo',
	'benc',
	'kurwa',
	'ogunie',
	'awizisie',
	'tego typu',
	'akat'
]){
	/*
	 * Melinskrypt
	 * The kurwoskrypt modification
	 *
	 * Inserts 'elo', 'benc', 'kurwa', 'ogunie',
	 * 'awizisie', 'tego typu' and 'akat' in random places
	 *
	 * Usage:
	 *  melinskrypt(document.body);
	 *  melinskrypt(document.body, ['fuq', 'blyat']);
	 * or
		document.getElementById('melinbutton').addEventListener('click', function(){
			melinskrypt(document.body);
		}, false);
	 *
	 * Source:
	 *  http://www.wykop.pl/wpis/20839033/przerobilem-ogunie-#!$%@?-skrypt-na-melinscript-wst/
	 */

	function nextLetter()
	{
		if(curNode == null)
			return null;

		if(curIndex >= curNode.data.length)
		{
			curNode=stack[++curNIndex];
			curIndex=-1;

			if(curNode == null)
				return null;
		}

		curIndex++;

		if(curIndex >= curNode.data.length)
			return ' ';

		return curNode.data[curIndex];
	}

	function nextAfterWord()
	{
		var a=null;

		while(true)
		{
			do
			{
				a=nextLetter();

				if(a == null)
					return false;
			}
			while(whitespace.indexOf(a) != -1);

			if(state == 1)
			{
				state=2;

				return true;
			}

			state=3;
			var b=curIndex;

			do
			{
				a=nextLetter();

				if(a == null)
					return false;

				if(state == 1)
					continue;
			}
			while(whitespace.indexOf(a) == -1);

			lastword=curNode.data.substr(b, curIndex-b);

			return true;
		}
	}

	function putHere(a)
	{
		if(curNode != null)
		{
			curNode.data=curNode.data.substr(0, curIndex)+(state != 2 ? ' ' : '')+a+(state == 2 ? ' ' : '')+curNode.data.substr(curIndex);
			curIndex+=a.length+2;
		}
	}

	function przelec(a)
	{
		if(
			(typeof a == 'object') &&
			(typeof a.childNodes == 'object')
		)
			for(
				var b=a.childNodes,
					c=0;
				c < b.length;
				c++
			){
				var d=b[c];

				if(
					(d.nodeType == 1) &&
					(d.tagName != 'SCRIPT') &&
					(d.tagName != 'IFRAME') &&
					(d.tagName != 'PRE')
				)
					przelec(d);

				if(d.nodeType == 3)
					stack.push(d);
			}
	}

	var whitespace="/|.,!:;'\" \t\n\r";
	var stack=[];
	var curNIndex=0;
	var curNode=null;
	var curIndex=-1;
	var state=1;
	var lastword='';

	przelec(element);
	curNode=stack[0];

	for(
		var i=Math.floor(9*Math.random());
		nextAfterWord();
	)
		if(i-- <= 0)
		{
			if(lastword == 'na')
				continue;

			putHere(words[Math.round(Math.random()*(words.length-1))]);

			var i=2+Math.floor(8*Math.random());
		}
}