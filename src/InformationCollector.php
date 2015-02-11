<?php
namespace wapmorgan\ServerStat;

use \COM;

class InformationCollector {
    protected $windows = false;
    protected $linux = false;

    public function __construct() {
        if (strncasecmp(PHP_OS, 'win', 3) === 0) {
            $this->windows = true;
        } else {
            $this->linux = true;
        }
    }

    public function collect() {
        $information = array
        (
            'processors' => $this->collectProccessorsNumber(),
            'processor_load' => $this->collectProcessorLoad(),
            'memory' => array('total' => $this->collectMemoryTotal(), 'free' => $this->collectMemoryFree()),
            'swap' => array('total' => $this->collectSwapTotal(), 'free' => $this->collectSwapFree()),
            'tasks' => $this->collectTasksNumber(),
            'uptime' => $this->collectUptime(),
        );
        $information['memory']['busy'] = $information['memory']['total'] - $information['memory']['free'];
        $information['swap']['busy'] = $information['swap']['total'] - $information['swap']['free'];
        return $information;
    }

    protected function collectProccessorsNumber() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('wmic cpu get NumberOfLogicalProcessors', $p);
            return intval($p[1]);
        }
    }

    protected function collectProcessorLoad() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('wmic cpu get LoadPercentage', $p);
            array_shift($p);
            return array_sum($p);
        }
    }

    protected function collectMemoryTotal() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('wmic computersystem get TotalPhysicalMemory', $p);
            return floatval($p[1]);
        }
    }

    protected function collectMemoryFree() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('wmic os get FreeVirtualMemory', $p);
            return floatval($p[1]);
        }
    }

    protected function collectSwapTotal() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('wmic os get TotalSwapSpaceSize', $p);
            array_shift($p);
            return array_sum($p);
        }
    }

    protected function collectSwapFree() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('wmic os get FreeSpaceInPagingFiles', $p);
            return floatval($p[1]);
        }
    }

    protected function collectTasksNumber() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('tasklist /FO csv', $p);
            return count($p) - 2;
        }
    }

    protected function collectTasksRunningNumber() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('tasklist /FI "STATUS eq RUNNING" /FO csv', $p);
            return count($p) - 2;
        }
    }

    protected function collectUptime() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            exec('wmic os get LastBootUpTime', $p);
            $v = trim($p[1]);
            $time = mktime(substr($v, 8, 2), substr($v, 10, 2), substr($v, 12, 2), substr($v, 4, 2), substr($v, 6, 2), substr($v, 0, 4));
            return time() - $time;
        }
    }
}
