<?php
class FileUtils{
	public static function checkDir($path, $permissions = 0755){
		if(is_dir($path))return true;
		mkdir($path, $permissions, true);
		if(is_dir($path))return true;
	}

	public static function getLocalFileContents($path){
		$handle = fopen($path, "r");
		$contents = fread($handle, filesize($path));
		fclose($handle);
		return $contents;
	}
	public static function getUniqueFileName($path){
		if(!is_file($path)) return $path;
		$pathInfo = pathinfo($path);
		$dir = $pathInfo['dirname'];
		$filename = $pathInfo['basename'];
		$ext = $pathInfo['extension'];
		$filenameNoExt = $pathInfo['filename'];
		$dir = dirname($path);
		$current = 1;
		$regex = "/^".$filenameNoExt."\\[([0-9][0-9]*)\\]\\.".$ext."$/";
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if(preg_match($regex, $file, $matches)>0){
						$intVal = intval($matches[1], 10);
						if($intVal >= $current) $current = $intVal+1;
					}
				}
				closedir($dh);
			}
		}else{
			//Thoroughly weird if $file is a file but there is no dir
			return NULL;
		}
		return $dir."/".$filenameNoExt."[".$current."].".$ext;
	}
	public static function readBinary($location){
		try{
			$file = fopen($location, "rb");
			$data = fread($file, filesize($location));
			fclose($file);
			return $data;
		}catch(Exception $ex){
			throw $ex;
		}
	}
    public static function  removeEmptyDirs($dir, $stopAt=null){
        if($stopAt == null) $stopAt = $GLOBALS['SITE_ROOT'];
        if($dir != "" && $dir != "."){
            if($dh  = opendir($dir)){
                $empty = true;
                while ((false !== ($filename = readdir($dh)))&&$empty) {
                    $empty = ($filename=="."||$filename=="..");
                }
                closedir($dh);
                if($empty){
                    rmdir($dir);
                    //go up folder
                    $lastSlash = strrpos($dir, "/");
                    if($lastSlash!==false){
                        $next = substr($dir, 0, $lastSlash);
                        if(strtolower($next)!=strtolower($stopAt)){
                            removeEmptyDirs($next, $stopAt);
                        }
                    }
                }
            }
        }
    }
    public static function isImage($path){
        return isContentTypeImage(getAppropriateContentType($path));
    }
    public static function isContentTypeImage($contentType){
        return (preg_match('/^image\/.*$/', $contentType)>0);
    }
	public static function getAppropriateContentType($path){
		//if(!is_file($path)) return "";
		$pathInfo = pathinfo($path);
		$ext = $pathInfo['extension'];
		$contentType = "";
		switch(strtolower($ext)){
			case "3dm":
			$contentType = "x-world/x-3dmf";
			break;
			case "3dmf":
			$contentType = "x-world/x-3dmf";
			break;
			case "a":
			$contentType = "application/octet-stream";
			break;
			case "aab":
			$contentType = "application/x-authorware-bin";
			break;
			case "aam":
			$contentType = "application/x-authorware-map";
			break;
			case "aas":
			$contentType = "application/x-authorware-seg";
			break;
			case "abc":
			$contentType = "text/vnd.abc";
			break;
			case "acgi":
			$contentType = "text/html";
			break;
			case "afl":
			$contentType = "video/animaflex";
			break;
			case "ai":
			$contentType = "application/postscript";
			break;
			case "aif":
			$contentType = "audio/aiff";
			break;
			case "aif":
			$contentType = "audio/x-aiff";
			break;
			case "aifc":
			$contentType = "audio/aiff";
			break;
			case "aifc":
			$contentType = "audio/x-aiff";
			break;
			case "aiff":
			$contentType = "audio/aiff";
			break;
			case "aiff":
			$contentType = "audio/x-aiff";
			break;
			case "aim":
			$contentType = "application/x-aim";
			break;
			case "aip":
			$contentType = "text/x-audiosoft-intra";
			break;
			case "ani":
			$contentType = "application/x-navi-animation";
			break;
			case "aos":
			$contentType = "application/x-nokia-9000-communicator-add-on-software";
			break;
			case "aps":
			$contentType = "application/mime";
			break;
			case "arc":
			$contentType = "application/octet-stream";
			break;
			case "arj":
			$contentType = "application/arj";
			break;
			case "arj":
			$contentType = "application/octet-stream";
			break;
			case "art":
			$contentType = "image/x-jg";
			break;
			case "asf":
			$contentType = "video/x-ms-asf";
			break;
			case "asm":
			$contentType = "text/x-asm";
			break;
			case "asp":
			$contentType = "text/asp";
			break;
			case "asx":
			$contentType = "application/x-mplayer2";
			break;
			case "asx":
			$contentType = "video/x-ms-asf";
			break;
			case "asx":
			$contentType = "video/x-ms-asf-plugin";
			break;
			case "au":
			$contentType = "audio/basic";
			break;
			case "au":
			$contentType = "audio/x-au";
			break;
			case "avi":
			$contentType = "application/x-troff-msvideo";
			break;
			case "avi":
			$contentType = "video/avi";
			break;
			case "avi":
			$contentType = "video/msvideo";
			break;
			case "avi":
			$contentType = "video/x-msvideo";
			break;
			case "avs":
			$contentType = "video/avs-video";
			break;
			case "bcpio":
			$contentType = "application/x-bcpio";
			break;
			case "bin":
			$contentType = "application/mac-binary";
			break;
			case "bin":
			$contentType = "application/macbinary";
			break;
			case "bin":
			$contentType = "application/octet-stream";
			break;
			case "bin":
			$contentType = "application/x-binary";
			break;
			case "bin":
			$contentType = "application/x-macbinary";
			break;
			case "bm":
			$contentType = "image/bmp";
			break;
			case "bmp":
			$contentType = "image/bmp";
			break;
			case "bmp":
			$contentType = "image/x-windows-bmp";
			break;
			case "boo":
			$contentType = "application/book";
			break;
			case "book":
			$contentType = "application/book";
			break;
			case "boz":
			$contentType = "application/x-bzip2";
			break;
			case "bsh":
			$contentType = "application/x-bsh";
			break;
			case "bz":
			$contentType = "application/x-bzip";
			break;
			case "bz2":
			$contentType = "application/x-bzip2";
			break;
			case "c":
			$contentType = "text/plain";
			break;
			case "c":
			$contentType = "text/x-c";
			break;
			case "c++":
			$contentType = "text/plain";
			break;
			case "cat":
			$contentType = "application/vnd.ms-pki.seccat";
			break;
			case "cc":
			$contentType = "text/plain";
			break;
			case "cc":
			$contentType = "text/x-c";
			break;
			case "ccad":
			$contentType = "application/clariscad";
			break;
			case "cco":
			$contentType = "application/x-cocoa";
			break;
			case "cdf":
			$contentType = "application/cdf";
			break;
			case "cdf":
			$contentType = "application/x-cdf";
			break;
			case "cdf":
			$contentType = "application/x-netcdf";
			break;
			case "cer":
			$contentType = "application/pkix-cert";
			break;
			case "cer":
			$contentType = "application/x-x509-ca-cert";
			break;
			case "cha":
			$contentType = "application/x-chat";
			break;
			case "chat":
			$contentType = "application/x-chat";
			break;
			case "class":
			$contentType = "application/java";
			break;
			case "class":
			$contentType = "application/java-byte-code";
			break;
			case "class":
			$contentType = "application/x-java-class";
			break;
			case "com":
			$contentType = "application/octet-stream";
			break;
			case "com":
			$contentType = "text/plain";
			break;
			case "conf":
			$contentType = "text/plain";
			break;
			case "cpio":
			$contentType = "application/x-cpio";
			break;
			case "cpp":
			$contentType = "text/x-c";
			break;
			case "cpt":
			$contentType = "application/mac-compactpro";
			break;
			case "cpt":
			$contentType = "application/x-compactpro";
			break;
			case "cpt":
			$contentType = "application/x-cpt";
			break;
			case "crl":
			$contentType = "application/pkcs-crl";
			break;
			case "crl":
			$contentType = "application/pkix-crl";
			break;
			case "crt":
			$contentType = "application/pkix-cert";
			break;
			case "crt":
			$contentType = "application/x-x509-ca-cert";
			break;
			case "crt":
			$contentType = "application/x-x509-user-cert";
			break;
			case "csh":
			$contentType = "application/x-csh";
			break;
			case "csh":
			$contentType = "text/x-script.csh";
			break;
			case "css":
			$contentType = "text/css";
			break;
			case "cxx":
			$contentType = "text/plain";
			break;
			case "dcr":
			$contentType = "application/x-director";
			break;
			case "deepv":
			$contentType = "application/x-deepv";
			break;
			case "def":
			$contentType = "text/plain";
			break;
			case "der":
			$contentType = "application/x-x509-ca-cert";
			break;
			case "dif":
			$contentType = "video/x-dv";
			break;
			case "dir":
			$contentType = "application/x-director";
			break;
			case "dl":
			$contentType = "video/dl";
			break;
			case "dl":
			$contentType = "video/x-dl";
			break;
			case "doc":
			$contentType = "application/msword";
			break;
			case "dot":
			$contentType = "application/msword";
			break;
			case "dp":
			$contentType = "application/commonground";
			break;
			case "drw":
			$contentType = "application/drafting";
			break;
			case "dump":
			$contentType = "application/octet-stream";
			break;
			case "dv":
			$contentType = "video/x-dv";
			break;
			case "dvi":
			$contentType = "application/x-dvi";
			break;
			case "dwf":
			$contentType = "drawing/x-dwf";
			break;
			case "dwg":
			$contentType = "application/acad";
			break;
			case "dwg":
			$contentType = "image/vnd.dwg";
			break;
			case "dwg":
			$contentType = "image/x-dwg";
			break;
			case "dxf":
			$contentType = "application/dxf";
			break;
			case "dxf":
			$contentType = "image/vnd.dwg";
			break;
			case "dxf":
			$contentType = "image/x-dwg";
			break;
			case "dxr":
			$contentType = "application/x-director";
			break;
			case "el":
			$contentType = "text/x-script.elisp";
			break;
			case "elc":
			$contentType = "application/x-bytecode.elisp";
			break;
			case "env":
			$contentType = "application/x-envoy";
			break;
			case "eps":
			$contentType = "application/postscript";
			break;
			case "es":
			$contentType = "application/x-esrehber";
			break;
			case "etx":
			$contentType = "text/x-setext";
			break;
			case "evy":
			$contentType = "application/envoy";
			break;
			case "evy":
			$contentType = "application/x-envoy";
			break;
			case "exe":
			$contentType = "application/octet-stream";
			break;
			case "f":
			$contentType = "text/plain";
			break;
			case "f":
			$contentType = "text/x-fortran";
			break;
			case "f77":
			$contentType = "text/x-fortran";
			break;
			case "f90":
			$contentType = "text/plain";
			break;
			case "f90":
			$contentType = "text/x-fortran";
			break;
			case "fdf":
			$contentType = "application/vnd.fdf";
			break;
			case "fif":
			$contentType = "application/fractals";
			break;
			case "fif":
			$contentType = "image/fif";
			break;
			case "fli":
			$contentType = "video/fli";
			break;
			case "fli":
			$contentType = "video/x-fli";
			break;
			case "flo":
			$contentType = "image/florian";
			break;
			case "flx":
			$contentType = "text/vnd.fmi.flexstor";
			break;
			case "fmf":
			$contentType = "video/x-atomic3d-feature";
			break;
			case "for":
			$contentType = "text/plain";
			break;
			case "for":
			$contentType = "text/x-fortran";
			break;
			case "fpx":
			$contentType = "image/vnd.fpx";
			break;
			case "fpx":
			$contentType = "image/vnd.net-fpx";
			break;
			case "frl":
			$contentType = "application/freeloader";
			break;
			case "funk":
			$contentType = "audio/make";
			break;
			case "g":
			$contentType = "text/plain";
			break;
			case "g3":
			$contentType = "image/g3fax";
			break;
			case "gif":
			$contentType = "image/gif";
			break;
			case "gl":
			$contentType = "video/gl";
			break;
			case "gl":
			$contentType = "video/x-gl";
			break;
			case "gsd":
			$contentType = "audio/x-gsm";
			break;
			case "gsm":
			$contentType = "audio/x-gsm";
			break;
			case "gsp":
			$contentType = "application/x-gsp";
			break;
			case "gss":
			$contentType = "application/x-gss";
			break;
			case "gtar":
			$contentType = "application/x-gtar";
			break;
			case "gz":
			$contentType = "application/x-compressed";
			break;
			case "gz":
			$contentType = "application/x-gzip";
			break;
			case "gzip":
			$contentType = "application/x-gzip";
			break;
			case "gzip":
			$contentType = "multipart/x-gzip";
			break;
			case "h":
			$contentType = "text/plain";
			break;
			case "h":
			$contentType = "text/x-h";
			break;
			case "hdf":
			$contentType = "application/x-hdf";
			break;
			case "help":
			$contentType = "application/x-helpfile";
			break;
			case "hgl":
			$contentType = "application/vnd.hp-hpgl";
			break;
			case "hh":
			$contentType = "text/plain";
			break;
			case "hh":
			$contentType = "text/x-h";
			break;
			case "hlb":
			$contentType = "text/x-script";
			break;
			case "hlp":
			$contentType = "application/hlp";
			break;
			case "hlp":
			$contentType = "application/x-helpfile";
			break;
			case "hlp":
			$contentType = "application/x-winhelp";
			break;
			case "hpg":
			$contentType = "application/vnd.hp-hpgl";
			break;
			case "hpgl":
			$contentType = "application/vnd.hp-hpgl";
			break;
			case "hqx":
			$contentType = "application/binhex";
			break;
			case "hqx":
			$contentType = "application/binhex4";
			break;
			case "hqx":
			$contentType = "application/mac-binhex";
			break;
			case "hqx":
			$contentType = "application/mac-binhex40";
			break;
			case "hqx":
			$contentType = "application/x-binhex40";
			break;
			case "hqx":
			$contentType = "application/x-mac-binhex40";
			break;
			case "hta":
			$contentType = "application/hta";
			break;
			case "htc":
			$contentType = "text/x-component";
			break;
			case "htm":
			$contentType = "text/html";
			break;
			case "html":
			$contentType = "text/html";
			break;
			case "htmls":
			$contentType = "text/html";
			break;
			case "htt":
			$contentType = "text/webviewhtml";
			break;
			case "htx":
			$contentType = "text/html";
			break;
			case "ice":
			$contentType = "x-conference/x-cooltalk";
			break;
			case "ico":
			$contentType = "image/x-icon";
			break;
			case "idc":
			$contentType = "text/plain";
			break;
			case "ief":
			$contentType = "image/ief";
			break;
			case "iefs":
			$contentType = "image/ief";
			break;
			case "iges":
			$contentType = "application/iges";
			break;
			case "iges":
			$contentType = "model/iges";
			break;
			case "igs":
			$contentType = "application/iges";
			break;
			case "igs":
			$contentType = "model/iges";
			break;
			case "ima":
			$contentType = "application/x-ima";
			break;
			case "imap":
			$contentType = "application/x-httpd-imap";
			break;
			case "inf":
			$contentType = "application/inf";
			break;
			case "ins":
			$contentType = "application/x-internett-signup";
			break;
			case "ip":
			$contentType = "application/x-ip2";
			break;
			case "isu":
			$contentType = "video/x-isvideo";
			break;
			case "it":
			$contentType = "audio/it";
			break;
			case "iv":
			$contentType = "application/x-inventor";
			break;
			case "ivr":
			$contentType = "i-world/i-vrml";
			break;
			case "ivy":
			$contentType = "application/x-livescreen";
			break;
			case "jam":
			$contentType = "audio/x-jam";
			break;
                        case "jar":
                        $contentType = "application/java-archive";
                        break;
			case "jav":
			$contentType = "text/plain";
			break;
			case "jav":
			$contentType = "text/x-java-source";
			break;
			case "java":
			$contentType = "text/plain";
			break;
			case "java":
			$contentType = "text/x-java-source";
			break;
			case "jcm":
			$contentType = "application/x-java-commerce";
			break;
			case "jfif":
			$contentType = "image/jpeg";
			break;
			case "jfif":
			$contentType = "image/pjpeg";
			break;
			case "jfif-tbnl":
			$contentType = "image/jpeg";
			break;
			case "jpe":
			$contentType = "image/jpeg";
			break;
			case "jpe":
			$contentType = "image/pjpeg";
			break;
			case "jpeg":
			$contentType = "image/jpeg";
			break;
			case "jpeg":
			$contentType = "image/pjpeg";
			break;
			case "jpg":
			$contentType = "image/jpeg";
			break;
			case "jpg":
			$contentType = "image/pjpeg";
			break;
			case "jps":
			$contentType = "image/x-jps";
			break;
			case "js":
			$contentType = "application/x-javascript";
			break;
			case "jut":
			$contentType = "image/jutvision";
			break;
			case "kar":
			$contentType = "audio/midi";
			break;
			case "kar":
			$contentType = "music/x-karaoke";
			break;
			case "ksh":
			$contentType = "application/x-ksh";
			break;
			case "ksh":
			$contentType = "text/x-script.ksh";
			break;
			case "la":
			$contentType = "audio/nspaudio";
			break;
			case "la":
			$contentType = "audio/x-nspaudio";
			break;
			case "lam":
			$contentType = "audio/x-liveaudio";
			break;
			case "latex":
			$contentType = "application/x-latex";
			break;
			case "lha":
			$contentType = "application/lha";
			break;
			case "lha":
			$contentType = "application/octet-stream";
			break;
			case "lha":
			$contentType = "application/x-lha";
			break;
			case "lhx":
			$contentType = "application/octet-stream";
			break;
			case "list":
			$contentType = "text/plain";
			break;
			case "lma":
			$contentType = "audio/nspaudio";
			break;
			case "lma":
			$contentType = "audio/x-nspaudio";
			break;
			case "log":
			$contentType = "text/plain";
			break;
			case "lsp":
			$contentType = "application/x-lisp";
			break;
			case "lsp":
			$contentType = "text/x-script.lisp";
			break;
			case "lst":
			$contentType = "text/plain";
			break;
			case "lsx":
			$contentType = "text/x-la-asf";
			break;
			case "ltx":
			$contentType = "application/x-latex";
			break;
			case "lzh":
			$contentType = "application/octet-stream";
			break;
			case "lzh":
			$contentType = "application/x-lzh";
			break;
			case "lzx":
			$contentType = "application/lzx";
			break;
			case "lzx":
			$contentType = "application/octet-stream";
			break;
			case "lzx":
			$contentType = "application/x-lzx";
			break;
			case "m":
			$contentType = "text/plain";
			break;
			case "m":
			$contentType = "text/x-m";
			break;
			case "m1v":
			$contentType = "video/mpeg";
			break;
			case "m2a":
			$contentType = "audio/mpeg";
			break;
			case "m2v":
			$contentType = "video/mpeg";
			break;
			case "m3u":
			$contentType = "audio/x-mpequrl";
			break;
			case "man":
			$contentType = "application/x-troff-man";
			break;
			case "map":
			$contentType = "application/x-navimap";
			break;
			case "mar":
			$contentType = "text/plain";
			break;
			case "mbd":
			$contentType = "application/mbedlet";
			break;
			case "mc$":
			$contentType = "application/x-magic-cap-package-1.0";
			break;
			case "mcd":
			$contentType = "application/mcad";
			break;
			case "mcd":
			$contentType = "application/x-mathcad";
			break;
			case "mcf":
			$contentType = "image/vasa";
			break;
			case "mcf":
			$contentType = "text/mcf";
			break;
			case "mcp":
			$contentType = "application/netmc";
			break;
			case "me":
			$contentType = "application/x-troff-me";
			break;
			case "mht":
			$contentType = "message/rfc822";
			break;
			case "mhtml":
			$contentType = "message/rfc822";
			break;
			case "mid":
			$contentType = "application/x-midi";
			break;
			case "mid":
			$contentType = "audio/midi";
			break;
			case "mid":
			$contentType = "audio/x-mid";
			break;
			case "mid":
			$contentType = "audio/x-midi";
			break;
			case "mid":
			$contentType = "music/crescendo";
			break;
			case "mid":
			$contentType = "x-music/x-midi";
			break;
			case "midi":
			$contentType = "application/x-midi";
			break;
			case "midi":
			$contentType = "audio/midi";
			break;
			case "midi":
			$contentType = "audio/x-mid";
			break;
			case "midi":
			$contentType = "audio/x-midi";
			break;
			case "midi":
			$contentType = "music/crescendo";
			break;
			case "midi":
			$contentType = "x-music/x-midi";
			break;
			case "mif":
			$contentType = "application/x-frame";
			break;
			case "mif":
			$contentType = "application/x-mif";
			break;
			case "mime":
			$contentType = "message/rfc822";
			break;
			case "mime":
			$contentType = "www/mime";
			break;
			case "mjf":
			$contentType = "audio/x-vnd.audioexplosion.mjuicemediafile";
			break;
			case "mjpg":
			$contentType = "video/x-motion-jpeg";
			break;
			case "mm":
			$contentType = "application/base64";
			break;
			case "mm":
			$contentType = "application/x-meme";
			break;
			case "mme":
			$contentType = "application/base64";
			break;
			case "mod":
			$contentType = "audio/mod";
			break;
			case "mod":
			$contentType = "audio/x-mod";
			break;
			case "moov":
			$contentType = "video/quicktime";
			break;
			case "mov":
			$contentType = "video/quicktime";
			break;
			case "movie":
			$contentType = "video/x-sgi-movie";
			break;
			case "mp2":
			$contentType = "audio/mpeg";
			break;
			case "mp2":
			$contentType = "audio/x-mpeg";
			break;
			case "mp2":
			$contentType = "video/mpeg";
			break;
			case "mp2":
			$contentType = "video/x-mpeg";
			break;
			case "mp2":
			$contentType = "video/x-mpeq2a";
			break;
			case "mp3":
			$contentType = "audio/mpeg3";
			break;
			case "mp3":
			$contentType = "audio/x-mpeg-3";
			break;
			case "mp3":
			$contentType = "video/mpeg";
			break;
			case "mp3":
			$contentType = "video/x-mpeg";
			break;
			case "mpa":
			$contentType = "audio/mpeg";
			break;
			case "mpa":
			$contentType = "video/mpeg";
			break;
			case "mpc":
			$contentType = "application/x-project";
			break;
			case "mpe":
			$contentType = "video/mpeg";
			break;
			case "mpeg":
			$contentType = "video/mpeg";
			break;
			case "mpg":
			$contentType = "audio/mpeg";
			break;
			case "mpg":
			$contentType = "video/mpeg";
			break;
			case "mpga":
			$contentType = "audio/mpeg";
			break;
			case "mpp":
			$contentType = "application/vnd.ms-project";
			break;
			case "mpt":
			$contentType = "application/x-project";
			break;
			case "mpv":
			$contentType = "application/x-project";
			break;
			case "mpx":
			$contentType = "application/x-project";
			break;
			case "mrc":
			$contentType = "application/marc";
			break;
			case "ms":
			$contentType = "application/x-troff-ms";
			break;
			case "mv":
			$contentType = "video/x-sgi-movie";
			break;
			case "my":
			$contentType = "audio/make";
			break;
			case "mzz":
			$contentType = "application/x-vnd.audioexplosion.mzz";
			break;
			case "nap":
			$contentType = "image/naplps";
			break;
			case "naplps":
			$contentType = "image/naplps";
			break;
			case "nc":
			$contentType = "application/x-netcdf";
			break;
			case "ncm":
			$contentType = "application/vnd.nokia.configuration-message";
			break;
			case "nif":
			$contentType = "image/x-niff";
			break;
			case "niff":
			$contentType = "image/x-niff";
			break;
			case "nix":
			$contentType = "application/x-mix-transfer";
			break;
			case "nsc":
			$contentType = "application/x-conference";
			break;
			case "nvd":
			$contentType = "application/x-navidoc";
			break;
			case "o":
			$contentType = "application/octet-stream";
			break;
			case "oda":
			$contentType = "application/oda";
			break;
			case "omc":
			$contentType = "application/x-omc";
			break;
			case "omcd":
			$contentType = "application/x-omcdatamaker";
			break;
			case "omcr":
			$contentType = "application/x-omcregerator";
			break;
			case "p":
			$contentType = "text/x-pascal";
			break;
			case "p10":
			$contentType = "application/pkcs10";
			break;
			case "p10":
			$contentType = "application/x-pkcs10";
			break;
			case "p12":
			$contentType = "application/pkcs-12";
			break;
			case "p12":
			$contentType = "application/x-pkcs12";
			break;
			case "p7a":
			$contentType = "application/x-pkcs7-signature";
			break;
			case "p7c":
			$contentType = "application/pkcs7-mime";
			break;
			case "p7c":
			$contentType = "application/x-pkcs7-mime";
			break;
			case "p7m":
			$contentType = "application/pkcs7-mime";
			break;
			case "p7m":
			$contentType = "application/x-pkcs7-mime";
			break;
			case "p7r":
			$contentType = "application/x-pkcs7-certreqresp";
			break;
			case "p7s":
			$contentType = "application/pkcs7-signature";
			break;
			case "part":
			$contentType = "application/pro_eng";
			break;
			case "pas":
			$contentType = "text/pascal";
			break;
			case "pbm":
			$contentType = "image/x-portable-bitmap";
			break;
			case "pcl":
			$contentType = "application/vnd.hp-pcl";
			break;
			case "pcl":
			$contentType = "application/x-pcl";
			break;
			case "pct":
			$contentType = "image/x-pict";
			break;
			case "pcx":
			$contentType = "image/x-pcx";
			break;
			case "pdb":
			$contentType = "chemical/x-pdb";
			break;
			case "pdf":
			$contentType = "application/pdf";
			break;
			case "pfunk":
			$contentType = "audio/make";
			break;
			case "pfunk":
			$contentType = "audio/make.my.funk";
			break;
			case "pgm":
			$contentType = "image/x-portable-graymap";
			break;
			case "pgm":
			$contentType = "image/x-portable-greymap";
			break;
			case "pic":
			$contentType = "image/pict";
			break;
			case "pict":
			$contentType = "image/pict";
			break;
			case "pkg":
			$contentType = "application/x-newton-compatible-pkg";
			break;
			case "pko":
			$contentType = "application/vnd.ms-pki.pko";
			break;
			case "pl":
			$contentType = "text/plain";
			break;
			case "pl":
			$contentType = "text/x-script.perl";
			break;
			case "plx":
			$contentType = "application/x-pixclscript";
			break;
			case "pm":
			$contentType = "image/x-xpixmap";
			break;
			case "pm":
			$contentType = "text/x-script.perl-module";
			break;
			case "pm4":
			$contentType = "application/x-pagemaker";
			break;
			case "pm5":
			$contentType = "application/x-pagemaker";
			break;
			case "png":
			$contentType = "image/png";
			break;
			case "pnm":
			$contentType = "application/x-portable-anymap";
			break;
			case "pnm":
			$contentType = "image/x-portable-anymap";
			break;
			case "pot":
			$contentType = "application/mspowerpoint";
			break;
			case "pot":
			$contentType = "application/vnd.ms-powerpoint";
			break;
			case "pov":
			$contentType = "model/x-pov";
			break;
			case "ppa":
			$contentType = "application/vnd.ms-powerpoint";
			break;
			case "ppm":
			$contentType = "image/x-portable-pixmap";
			break;
			case "pps":
			$contentType = "application/mspowerpoint";
			break;
			case "pps":
			$contentType = "application/vnd.ms-powerpoint";
			break;
			case "ppt":
			$contentType = "application/mspowerpoint";
			break;
			case "ppt":
			$contentType = "application/powerpoint";
			break;
			case "ppt":
			$contentType = "application/vnd.ms-powerpoint";
			break;
			case "ppt":
			$contentType = "application/x-mspowerpoint";
			break;
			case "ppz":
			$contentType = "application/mspowerpoint";
			break;
			case "pre":
			$contentType = "application/x-freelance";
			break;
			case "prt":
			$contentType = "application/pro_eng";
			break;
			case "ps":
			$contentType = "application/postscript";
			break;
			case "psd":
			$contentType = "application/octet-stream";
			break;
			case "pvu":
			$contentType = "paleovu/x-pv";
			break;
			case "pwz":
			$contentType = "application/vnd.ms-powerpoint";
			break;
			case "py":
			$contentType = "text/x-script.phyton";
			break;
			case "pyc":
			$contentType = "applicaiton/x-bytecode.python";
			break;
			case "qcp":
			$contentType = "audio/vnd.qcelp";
			break;
			case "qd3":
			$contentType = "x-world/x-3dmf";
			break;
			case "qd3d":
			$contentType = "x-world/x-3dmf";
			break;
			case "qif":
			$contentType = "image/x-quicktime";
			break;
			case "qt":
			$contentType = "video/quicktime";
			break;
			case "qtc":
			$contentType = "video/x-qtc";
			break;
			case "qti":
			$contentType = "image/x-quicktime";
			break;
			case "qtif":
			$contentType = "image/x-quicktime";
			break;
			case "ra":
			$contentType = "audio/x-pn-realaudio";
			break;
			case "ra":
			$contentType = "audio/x-pn-realaudio-plugin";
			break;
			case "ra":
			$contentType = "audio/x-realaudio";
			break;
			case "ram":
			$contentType = "audio/x-pn-realaudio";
			break;
			case "ras":
			$contentType = "application/x-cmu-raster";
			break;
			case "ras":
			$contentType = "image/cmu-raster";
			break;
			case "ras":
			$contentType = "image/x-cmu-raster";
			break;
			case "rast":
			$contentType = "image/cmu-raster";
			break;
			case "rexx":
			$contentType = "text/x-script.rexx";
			break;
			case "rf":
			$contentType = "image/vnd.rn-realflash";
			break;
			case "rgb":
			$contentType = "image/x-rgb";
			break;
			case "rm":
			$contentType = "application/vnd.rn-realmedia";
			break;
			case "rm":
			$contentType = "audio/x-pn-realaudio";
			break;
			case "rmi":
			$contentType = "audio/mid";
			break;
			case "rmm":
			$contentType = "audio/x-pn-realaudio";
			break;
			case "rmp":
			$contentType = "audio/x-pn-realaudio";
			break;
			case "rmp":
			$contentType = "audio/x-pn-realaudio-plugin";
			break;
			case "rng":
			$contentType = "application/ringing-tones";
			break;
			case "rng":
			$contentType = "application/vnd.nokia.ringing-tone";
			break;
			case "rnx":
			$contentType = "application/vnd.rn-realplayer";
			break;
			case "roff":
			$contentType = "application/x-troff";
			break;
			case "rp":
			$contentType = "image/vnd.rn-realpix";
			break;
			case "rpm":
			$contentType = "audio/x-pn-realaudio-plugin";
			break;
			case "rt":
			$contentType = "text/richtext";
			break;
			case "rt":
			$contentType = "text/vnd.rn-realtext";
			break;
			case "rtf":
			$contentType = "application/rtf";
			break;
			case "rtf":
			$contentType = "application/x-rtf";
			break;
			case "rtf":
			$contentType = "text/richtext";
			break;
			case "rtx":
			$contentType = "application/rtf";
			break;
			case "rtx":
			$contentType = "text/richtext";
			break;
			case "rv":
			$contentType = "video/vnd.rn-realvideo";
			break;
			case "s":
			$contentType = "text/x-asm";
			break;
			case "s3m":
			$contentType = "audio/s3m";
			break;
			case "saveme":
			$contentType = "application/octet-stream";
			break;
			case "sbk":
			$contentType = "application/x-tbook";
			break;
			case "scm":
			$contentType = "application/x-lotusscreencam";
			break;
			case "scm":
			$contentType = "text/x-script.guile";
			break;
			case "scm":
			$contentType = "text/x-script.scheme";
			break;
			case "scm":
			$contentType = "video/x-scm";
			break;
			case "sdml":
			$contentType = "text/plain";
			break;
			case "sdp":
			$contentType = "application/sdp";
			break;
			case "sdp":
			$contentType = "application/x-sdp";
			break;
			case "sdr":
			$contentType = "application/sounder";
			break;
			case "sea":
			$contentType = "application/sea";
			break;
			case "sea":
			$contentType = "application/x-sea";
			break;
			case "set":
			$contentType = "application/set";
			break;
			case "sgm":
			$contentType = "text/sgml";
			break;
			case "sgm":
			$contentType = "text/x-sgml";
			break;
			case "sgml":
			$contentType = "text/sgml";
			break;
			case "sgml":
			$contentType = "text/x-sgml";
			break;
			case "sh":
			$contentType = "application/x-bsh";
			break;
			case "sh":
			$contentType = "application/x-sh";
			break;
			case "sh":
			$contentType = "application/x-shar";
			break;
			case "sh":
			$contentType = "text/x-script.sh";
			break;
			case "shar":
			$contentType = "application/x-bsh";
			break;
			case "shar":
			$contentType = "application/x-shar";
			break;
			case "shtml":
			$contentType = "text/html";
			break;
			case "shtml":
			$contentType = "text/x-server-parsed-html";
			break;
			case "sid":
			$contentType = "audio/x-psid";
			break;
			case "sit":
			$contentType = "application/x-sit";
			break;
			case "sit":
			$contentType = "application/x-stuffit";
			break;
			case "skd":
			$contentType = "application/x-koan";
			break;
			case "skm":
			$contentType = "application/x-koan";
			break;
			case "skp":
			$contentType = "application/x-koan";
			break;
			case "skt":
			$contentType = "application/x-koan";
			break;
			case "sl":
			$contentType = "application/x-seelogo";
			break;
			case "smi":
			$contentType = "application/smil";
			break;
			case "smil":
			$contentType = "application/smil";
			break;
			case "snd":
			$contentType = "audio/basic";
			break;
			case "snd":
			$contentType = "audio/x-adpcm";
			break;
			case "sol":
			$contentType = "application/solids";
			break;
			case "spc":
			$contentType = "application/x-pkcs7-certificates";
			break;
			case "spc":
			$contentType = "text/x-speech";
			break;
			case "spl":
			$contentType = "application/futuresplash";
			break;
			case "spr":
			$contentType = "application/x-sprite";
			break;
			case "sprite":
			$contentType = "application/x-sprite";
			break;
			case "src":
			$contentType = "application/x-wais-source";
			break;
			case "ssi":
			$contentType = "text/x-server-parsed-html";
			break;
			case "ssm":
			$contentType = "application/streamingmedia";
			break;
			case "sst":
			$contentType = "application/vnd.ms-pki.certstore";
			break;
			case "step":
			$contentType = "application/step";
			break;
			case "stl":
			$contentType = "application/sla";
			break;
			case "stl":
			$contentType = "application/vnd.ms-pki.stl";
			break;
			case "stl":
			$contentType = "application/x-navistyle";
			break;
			case "stp":
			$contentType = "application/step";
			break;
			case "sv4cpio":
			$contentType = "application/x-sv4cpio";
			break;
			case "sv4crc":
			$contentType = "application/x-sv4crc";
			break;
			case "svf":
			$contentType = "image/vnd.dwg";
			break;
			case "svf":
			$contentType = "image/x-dwg";
			break;
			case "svr":
			$contentType = "application/x-world";
			break;
			case "svr":
			$contentType = "x-world/x-svr";
			break;
			case "swf":
			$contentType = "application/x-shockwave-flash";
			break;
			case "t":
			$contentType = "application/x-troff";
			break;
			case "talk":
			$contentType = "text/x-speech";
			break;
			case "tar":
			$contentType = "application/x-tar";
			break;
			case "tbk":
			$contentType = "application/toolbook";
			break;
			case "tbk":
			$contentType = "application/x-tbook";
			break;
			case "tcl":
			$contentType = "application/x-tcl";
			break;
			case "tcl":
			$contentType = "text/x-script.tcl";
			break;
			case "tcsh":
			$contentType = "text/x-script.tcsh";
			break;
			case "tex":
			$contentType = "application/x-tex";
			break;
			case "texi":
			$contentType = "application/x-texinfo";
			break;
			case "texinfo":
			$contentType = "application/x-texinfo";
			break;
			case "text":
			$contentType = "application/plain";
			break;
			case "text":
			$contentType = "text/plain";
			break;
			case "tgz":
			$contentType = "application/gnutar";
			break;
			case "tgz":
			$contentType = "application/x-compressed";
			break;
			case "tif":
			$contentType = "image/tiff";
			break;
			case "tif":
			$contentType = "image/x-tiff";
			break;
			case "tiff":
			$contentType = "image/tiff";
			break;
			case "tiff":
			$contentType = "image/x-tiff";
			break;
			case "tr":
			$contentType = "application/x-troff";
			break;
			case "tsi":
			$contentType = "audio/tsp-audio";
			break;
			case "tsp":
			$contentType = "application/dsptype";
			break;
			case "tsp":
			$contentType = "audio/tsplayer";
			break;
			case "tsv":
			$contentType = "text/tab-separated-values";
			break;
			case "turbot":
			$contentType = "image/florian";
			break;
			case "txt":
			$contentType = "text/plain";
			break;
			case "uil":
			$contentType = "text/x-uil";
			break;
			case "uni":
			$contentType = "text/uri-list";
			break;
			case "unis":
			$contentType = "text/uri-list";
			break;
			case "unv":
			$contentType = "application/i-deas";
			break;
			case "uri":
			$contentType = "text/uri-list";
			break;
			case "uris":
			$contentType = "text/uri-list";
			break;
			case "ustar":
			$contentType = "application/x-ustar";
			break;
			case "ustar":
			$contentType = "multipart/x-ustar";
			break;
			case "uu":
			$contentType = "application/octet-stream";
			break;
			case "uu":
			$contentType = "text/x-uuencode";
			break;
			case "uue":
			$contentType = "text/x-uuencode";
			break;
			case "vcd":
			$contentType = "application/x-cdlink";
			break;
			case "vcs":
			$contentType = "text/x-vcalendar";
			break;
			case "vda":
			$contentType = "application/vda";
			break;
			case "vdo":
			$contentType = "video/vdo";
			break;
			case "vew":
			$contentType = "application/groupwise";
			break;
			case "viv":
			$contentType = "video/vivo";
			break;
			case "viv":
			$contentType = "video/vnd.vivo";
			break;
			case "vivo":
			$contentType = "video/vivo";
			break;
			case "vivo":
			$contentType = "video/vnd.vivo";
			break;
			case "vmd":
			$contentType = "application/vocaltec-media-desc";
			break;
			case "vmf":
			$contentType = "application/vocaltec-media-file";
			break;
			case "voc":
			$contentType = "audio/voc";
			break;
			case "voc":
			$contentType = "audio/x-voc";
			break;
			case "vos":
			$contentType = "video/vosaic";
			break;
			case "vox":
			$contentType = "audio/voxware";
			break;
			case "vqe":
			$contentType = "audio/x-twinvq-plugin";
			break;
			case "vqf":
			$contentType = "audio/x-twinvq";
			break;
			case "vql":
			$contentType = "audio/x-twinvq-plugin";
			break;
			case "vrml":
			$contentType = "application/x-vrml";
			break;
			case "vrml":
			$contentType = "model/vrml";
			break;
			case "vrml":
			$contentType = "x-world/x-vrml";
			break;
			case "vrt":
			$contentType = "x-world/x-vrt";
			break;
			case "vsd":
			$contentType = "application/x-visio";
			break;
			case "vst":
			$contentType = "application/x-visio";
			break;
			case "vsw":
			$contentType = "application/x-visio";
			break;
			case "w60":
			$contentType = "application/wordperfect6.0";
			break;
			case "w61":
			$contentType = "application/wordperfect6.1";
			break;
			case "w6w":
			$contentType = "application/msword";
			break;
			case "wav":
			$contentType = "audio/wav";
			break;
			case "wav":
			$contentType = "audio/x-wav";
			break;
			case "wb1":
			$contentType = "application/x-qpro";
			break;
			case "wbmp":
			$contentType = "image/vnd.wap.wbmp";
			break;
			case "web":
			$contentType = "application/vnd.xara";
			break;
			case "wiz":
			$contentType = "application/msword";
			break;
			case "wk1":
			$contentType = "application/x-123";
			break;
			case "wmf":
			$contentType = "windows/metafile";
			break;
			case "wml":
			$contentType = "text/vnd.wap.wml";
			break;
			case "wmlc":
			$contentType = "application/vnd.wap.wmlc";
			break;
			case "wmls":
			$contentType = "text/vnd.wap.wmlscript";
			break;
			case "wmlsc":
			$contentType = "application/vnd.wap.wmlscriptc";
			break;
			case "word":
			$contentType = "application/msword";
			break;
			case "wp":
			$contentType = "application/wordperfect";
			break;
			case "wp5":
			$contentType = "application/wordperfect";
			break;
			case "wp5":
			$contentType = "application/wordperfect6.0";
			break;
			case "wp6":
			$contentType = "application/wordperfect";
			break;
			case "wpd":
			$contentType = "application/wordperfect";
			break;
			case "wpd":
			$contentType = "application/x-wpwin";
			break;
			case "wq1":
			$contentType = "application/x-lotus";
			break;
			case "wri":
			$contentType = "application/mswrite";
			break;
			case "wri":
			$contentType = "application/x-wri";
			break;
			case "wrl":
			$contentType = "application/x-world";
			break;
			case "wrl":
			$contentType = "model/vrml";
			break;
			case "wrl":
			$contentType = "x-world/x-vrml";
			break;
			case "wrz":
			$contentType = "model/vrml";
			break;
			case "wrz":
			$contentType = "x-world/x-vrml";
			break;
			case "wsc":
			$contentType = "text/scriplet";
			break;
			case "wsrc":
			$contentType = "application/x-wais-source";
			break;
			case "wtk":
			$contentType = "application/x-wintalk";
			break;
			case "xbm":
			$contentType = "image/x-xbitmap";
			break;
			case "xbm":
			$contentType = "image/x-xbm";
			break;
			case "xbm":
			$contentType = "image/xbm";
			break;
			case "xdr":
			$contentType = "video/x-amt-demorun";
			break;
			case "xgz":
			$contentType = "xgl/drawing";
			break;
			case "xif":
			$contentType = "image/vnd.xiff";
			break;
			case "xl":
			$contentType = "application/excel";
			break;
			case "xla":
			$contentType = "application/excel";
			break;
			case "xla":
			$contentType = "application/x-excel";
			break;
			case "xla":
			$contentType = "application/x-msexcel";
			break;
			case "xlb":
			$contentType = "application/excel";
			break;
			case "xlb":
			$contentType = "application/vnd.ms-excel";
			break;
			case "xlb":
			$contentType = "application/x-excel";
			break;
			case "xlc":
			$contentType = "application/excel";
			break;
			case "xlc":
			$contentType = "application/vnd.ms-excel";
			break;
			case "xlc":
			$contentType = "application/x-excel";
			break;
			case "xld":
			$contentType = "application/excel";
			break;
			case "xld":
			$contentType = "application/x-excel";
			break;
			case "xlk":
			$contentType = "application/excel";
			break;
			case "xlk":
			$contentType = "application/x-excel";
			break;
			case "xll":
			$contentType = "application/excel";
			break;
			case "xll":
			$contentType = "application/vnd.ms-excel";
			break;
			case "xll":
			$contentType = "application/x-excel";
			break;
			case "xlm":
			$contentType = "application/excel";
			break;
			case "xlm":
			$contentType = "application/vnd.ms-excel";
			break;
			case "xlm":
			$contentType = "application/x-excel";
			break;
			case "xls":
			$contentType = "application/excel";
			break;
			case "xls":
			$contentType = "application/vnd.ms-excel";
			break;
			case "xls":
			$contentType = "application/x-excel";
			break;
			case "xls":
			$contentType = "application/x-msexcel";
			break;
			case "xlt":
			$contentType = "application/excel";
			break;
			case "xlt":
			$contentType = "application/x-excel";
			break;
			case "xlv":
			$contentType = "application/excel";
			break;
			case "xlv":
			$contentType = "application/x-excel";
			break;
			case "xlw":
			$contentType = "application/excel";
			break;
			case "xlw":
			$contentType = "application/vnd.ms-excel";
			break;
			case "xlw":
			$contentType = "application/x-excel";
			break;
			case "xlw":
			$contentType = "application/x-msexcel";
			break;
			case "xm":
			$contentType = "audio/xm";
			break;
			case "xml":
			$contentType = "application/xml";
			break;
			case "xml":
			$contentType = "text/xml";
			break;
			case "xmz":
			$contentType = "xgl/movie";
			break;
			case "xpix":
			$contentType = "application/x-vnd.ls-xpix";
			break;
			case "xpm":
			$contentType = "image/x-xpixmap";
			break;
			case "xpm":
			$contentType = "image/xpm";
			break;
			case "x-png":
			$contentType = "image/png";
			break;
			case "xsr":
			$contentType = "video/x-amt-showrun";
			break;
			case "xwd":
			$contentType = "image/x-xwd";
			break;
			case "xwd":
			$contentType = "image/x-xwindowdump";
			break;
			case "xyz":
			$contentType = "chemical/x-pdb";
			break;
			case "z":
			$contentType = "application/x-compress";
			break;
			case "z":
			$contentType = "application/x-compressed";
			break;
			case "zip":
			$contentType = "application/x-compressed";
			break;
			case "zip":
			$contentType = "application/x-zip-compressed";
			break;
			case "zip":
			$contentType = "application/zip";
			break;
			case "zip":
			$contentType = "multipart/x-zip";
			break;
			case "zoo":
			$contentType = "application/octet-stream";
			break;
			case "zsh":
			$contentType = "text/x-script.zsh";
			break;
			default:
				$contentType = "text/plain";
		}
		return $contentType;
	}


}
?>