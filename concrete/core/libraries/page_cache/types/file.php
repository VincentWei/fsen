<?php

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_FilePageCache extends PageCache {

	public function getRecord($mixed) {
		$file = $this->getCacheFile($mixed);
		#error_log ("To get cached file: $file\n", 3, '/var/tmp/c5.log');
		if (file_exists($file)) {
			#error_log ("Got cached file: $file\n", 3, '/var/tmp/c5.log');
			$contents = file_get_contents($file);
			$record = @unserialize($contents);
			if ($record instanceof PageCacheRecord) {
				return $record;
			}
		}
	}

	/* WEIYM: tune to support cache for mobile */
	protected function getCacheFileWithPrefix($mixed, $prefix) {
		$key = $this->getCacheKey($mixed);
		$filename = $key . '.cache';

		if ($key) {
			/* WEIYM: the key always has pattern: [0-9a-f]{32}
			if (strlen($key) == 1) {
				$dir = $prefix . '/' . $key;
			} else if (strlen($key) == 2) {
				$dir = $prefix . '/' . $key[0] . '/' . $key[1];
			} else {
				$dir = $prefix . '/' . $key[0] . '/' . $key[1] . '/' . $key[2];
			}
			*/
			$dir = $prefix . '/' . $key[0] . '/' . $key[1] . '/' . $key[2];
			if ($dir && (!is_dir($dir))) {
				@mkdir($dir, DIRECTORY_PERMISSIONS_MODE, true);
			}
			$path = $dir . '/' . $filename;
			return $path;
		}
	}

	protected function getCacheFile($mixed) {
		Loader::library('3rdparty/mobile_detect');
		$md = new Mobile_Detect();
		if ($md->isMobile()) {
			$prefix = DIR_FILES_PAGE_CACHE_MOBILE;
		}
		else {
			$prefix = DIR_FILES_PAGE_CACHE;
		}

		return $this->getCacheFileWithPrefix($mixed, $prefix);
	}

	public function purgeByRecord(PageCacheRecord $rec) {
		$file = $this->getCacheFile($rec);
		if ($file && file_exists($file)) {
			@unlink($file);
		}
	}

	public function flush() {
		$fh = Loader::helper("file");
		$fh->removeAll(DIR_FILES_PAGE_CACHE);

		/* WEIYM: tune to support cache for mobile */
		$fh->removeAll(DIR_FILES_PAGE_CACHE_MOBILE);
	}

	/* WEIYM: tune to support cache for mobile */
	public function purge(Page $c) {
		$file = $this->getCacheFileWithPrefix($c, DIR_FILES_PAGE_CACHE);
		if ($file && file_exists($file)) {
			@unlink($file);
		}

		$file = $this->getCacheFileWithPrefix($c, DIR_FILES_PAGE_CACHE_MOBILE);
		if ($file && file_exists($file)) {
			@unlink($file);
		}
	}

	public function set(Page $c, $content) {
		if (!is_dir(DIR_FILES_PAGE_CACHE)) {
			@mkdir(DIR_FILES_PAGE_CACHE);
			@touch(DIR_FILES_PAGE_CACHE . '/index.html');
		}

		/* WEIYM: tune to support cache for mobile */
		if (!is_dir(DIR_FILES_PAGE_CACHE_MOBILE)) {
			@mkdir(DIR_FILES_PAGE_CACHE_MOBILE);
			@touch(DIR_FILES_PAGE_CACHE_MOBILE . '/index.html');
		}

		$lifetime = $c->getCollectionFullPageCachingLifetimeValue();
		$file = $this->getCacheFile($c);
		if ($file) {
			#error_log ("To save page to cache file: $file\n", 3, '/var/tmp/c5.log');
			$response = new PageCacheRecord($c, $content, $lifetime);
			if ($content) {
				file_put_contents($file, serialize($response));
			}
		}
	}
}
