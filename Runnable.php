<?php

declare(strict_types=1);

namespace Runnable {

	use BaseClassLoader;
	use client\Client;
	use client\RakNetPool;
	use pocketmine\entity\Attribute;
	use pocketmine\network\mcpe\protocol\PacketPool;
	use pocketmine\utils\Terminal;
	use function cli_set_process_title;
	use function define;
	use function getcwd;
	use function ini_set;
	use function realpath;

	info('Load MSClient..');
    info('Setting client...');

    @define('CLIENTPATH', realpath(getcwd()) . DIRECTORY_SEPARATOR);
    @define('CLIENTNAME', 'MSClient v1.0');
    @define("ENDIANNESS", (pack("d", 1) === "\77\360\0\0\0\0\0\0" ? 0 : 1));
    @define("INT32_MASK", is_int(0xffffffff) ? 0xffffffff : -1);

    @cli_set_process_title(CLIENTNAME);
    @ini_set('memory_limit', '-1');

	assert_options(ASSERT_ACTIVE, 1);
	assert_options(ASSERT_WARNING, 1);
	assert_options(ASSERT_QUIET_EVAL, 1);

    info('Run class loader..');

    if (!is_file(CLIENTPATH . "spl/ClassLoader.php")) {
        echo "[CRITICAL] Unable to find the client-SPL library." . PHP_EOL;
        echo "[CRITICAL] Please use provided builds or clone the repository recursively." . PHP_EOL;
        exit(1);
    }
    require_once(CLIENTPATH . "spl/ClassLoader.php");
    require_once(CLIENTPATH . "spl/BaseClassLoader.php");

    $autoloader = new BaseClassLoader();
    $autoloader->addPath(CLIENTPATH);
    $autoloader->register(true);

    info('Init all classes..');
    RakNetPool::init();
    PacketPool::init();
    Attribute::init();
    Terminal::init();

    info('Started client!');
    $client = new Client();
    while (true) $client->tick();
}

namespace {
    /**
     * @param string $text
     */
    function send(string $text)
    {
        echo colorize('%purple%LOG%gray% > %white%' . $text . '%reset%') . PHP_EOL;
    }

	/**
	 * @param string $text
	 */
	function debug(string $text)
	{
		echo colorize('%gray%DEBUG%gray% > %white%' . $text . '%reset%') . PHP_EOL;
	}

    /**
     * @param string $text
     */
    function info(string $text)
    {
        echo colorize('%aqua%INFO%gray% > %white%' . $text . '%reset%') . PHP_EOL;
    }

	/**
	 * @param string $text
	 */
	function error(string $text)
	{
		echo colorize('%red%ERROR%gray% > %white%' . $text . '%reset%') . PHP_EOL;
	}

    /**
     * @param string $type
     * @param string $text
     */
    function mess(string $type, string $text)
    {
        $array = explode("\n", $text);
        foreach ($array as $mess) if (preg_replace('/\s+/', '', $mess) !== '') echo colorize('%aqua%' . $type . '%gray% > %white%' . $mess . '%reset%') . PHP_EOL;
    }

    /**
     * @param string $text
     * @return mixed|string
	 *
	 * https://en.wikipedia.org/wiki/ANSI_escape_code
	 * ESC[ 38:5:⟨n⟩ m - Select foreground color
	 * ESC[ 48:5:⟨n⟩ m - Select background color
     */
    function colorize(string $text)
    {
        $text = str_replace('%aqua%', "\e[38;5;87m", $text);
        $text = str_replace('%white%', "\x1b[38;5;231m", $text);
        $text = str_replace('%red%', "\x1b[38;5;9m", $text);
        $text = str_replace('%reset%', "\x1b[m", $text);
        $text = str_replace('%gray%', "\x1b[38;5;145m", $text);
        $text = str_replace('%purple%', "\x1b[38;5;127m", $text);
        $text = str_replace('%green%', "\x1b[38;5;83m", $text);
        return $text;
    }

    /**
     * Shutdown client
     */
    function shutdown()
    {
        gc_collect_cycles();
        @kill(getmypid());
    }

    /**
     * @param $pid
     */
    function kill($pid)
    {
        switch (getOS()) {
            case "win":
                exec("taskkill.exe /F /PID " . ((int)$pid) . " > NUL");
                break;
            case "mac":
            case "linux":
            default:
                if (function_exists("posix_kill")) {
                    posix_kill($pid, SIGKILL);
                } else {
                    exec("kill -9 " . ((int)$pid) . " > /dev/null 2>&1");
                }
        }
    }

    /**
     * @param bool $recalculate
     * @return string
     */
    function getOS(bool $recalculate = false): string
    {
        $os = "other";
        if ($recalculate) {
            $uname = php_uname("s");
            if (stripos($uname, "Darwin") !== false) {
                if (strpos(php_uname("m"), "iP") === 0) {
                    $os = "ios";
                } else {
                    $os = "mac";
                }
            } elseif (stripos($uname, "Win") !== false or $uname === "Msys") {
                $os = "win";
            } elseif (stripos($uname, "Linux") !== false) {
                if (@file_exists("/system/build.prop")) {
                    $os = "android";
                } else {
                    $os = "linux";
                }
            } elseif (stripos($uname, "BSD") !== false or $uname === "DragonFly") {
                $os = "bsd";
            }
        }

        return $os;
    }
}