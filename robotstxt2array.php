<?php
/*
 * RobotsTXT2Array
 * Created January 13, 2013
 * 
 * Copyright (c) 2013, SOAChishti (soachishti@outlook.com).
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 * 
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SOAChishti BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

//Report all Errors
ini_set('display_errors', 'on');
error_reporting(E_ALL ^ E_NOTICE); 


/*======================================================================*\
	Function:	allowed
	Purpose:	It will check whether link is allowed in robottx file
	Input:		link to check, robottxt array, agent
	Output:		Absolute Url
\*======================================================================*/

function allowed($link,$array,$agent)
{
	global $url;
	$array = isset($array[$agent]['disallow']) ? $array[$agent]['disallow'] : false;
	if($array == true)
	{
		foreach($array as $value) {
			if(rel2abs($url,$link) === $value) 
			{
				
				return 'not allowed';
			}
		}
	}
	
	$array = isset($array[$agent]['allow']) ? $array[$agent]['allow'] : false;
	if($array == true)
	{
		foreach($array as $value) {
			if(rel2abs($url,$link) === $value) 
			{
				return 'allowed';
			}
		}
	}
	return 'allowed';
}
/*======================================================================*\
	END OF Function allowed
\*======================================================================*/

/*======================================================================*\
	Function:	rel2abs
	Purpose:	Convert Relative URL to Absolute Url
	Input:		Relative Url and Base Url
	Output:		Absolute Url
\*======================================================================*/

function rel2abs($base,$rel)
{
	global $rel2abs;
	if($rel2abs == true)
	{
		if (parse_url($rel, PHP_URL_SCHEME) != '') # return if already absolute URL 
		{
			return $rel;
		}
		
		if ($rel=='#' || $rel=='?') # queries and anchors 
		{
			return $base.$rel;
		}
		
		extract(parse_url($base)); # parse base URL and convert to local variables: $scheme, $host, $path
		$path = (isset($path)) ? $path : '/';
		$path = preg_replace('#/[^/]*$#', '', $path); # remove non-directory element from path
		
		if ($rel == '/') 
		{
			$path = '';
		}
		$abs = $host . $path . "/" . $rel;
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#'); # replace '//' or '/./' or '/foo/../' with '/'
		for($n = 1; $n > 0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}
		
		return $scheme.'://'.$abs; #absolute URL is ready!
	}
	else
	{
		return $rel;
	}
}

/*======================================================================*\
	END OF Function rel2abs
\*======================================================================*/

/*======================================================================*\
	Function:	download_curl
	Purpose:	It will get the html from the url
	Input:		url
	Output:		HTML
\*======================================================================*/

function download_curl($url)
{
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	$data = curl_exec($ch);
	curl_close($ch);
    return $data;
}

/*======================================================================*\
	END OF Function download_curl
\*======================================================================*/

/*======================================================================*\
	Function:	robotstxt_parse
	Purpose:	It will convert robottxt text to array
	Input:		robottxt content
	Output:		array
\*======================================================================*/

function robotstxt_parse($txt)
{
	global $url;
	$txt = trim($txt) . " ";
	preg_match_all("#(sitemap|allow|disallow|user-agent)\s*:\s*(.*?)\s+#is",$txt,$txt);
	for($i=0;$i<=count($txt[1])-1;$i++)
	{
		$txt[1][$i] = strtolower($txt[1][$i]);
		if($txt[1][$i] == 'sitemap')
		{
			$value['sitemap'][] = rel2abs($url,$txt[2][$i]);
		}
		else if($txt[1][$i] == 'user-agent')
		{
			$value[$txt[2][$i]] = '';
			$c = $txt[2][$i];
		}	
		else if($txt[1][$i] == 'allow')
		{
			$value[$c]['allow'][] =  rel2abs($url,$txt[2][$i]); 
		}
		else if($txt[1][$i] == 'disallow')
		{
			$value[$c]['disallow'][] = rel2abs($url,$txt[2][$i]); 
		}
	}
	ksort($value);
	return $value;
}

/*======================================================================*\
	END OF Function robotstxt_parse
\*======================================================================*/
?>