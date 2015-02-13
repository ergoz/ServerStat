<?php
namespace wapmorgan\ServerStat;

class InformationCollector {
    protected $windows = false;
    protected $linux = false;
    protected $memoryTotalCache = false;

    public function __construct() {
        if (strncasecmp(PHP_OS, 'win', 3) === 0) {
            $this->windows = new WindowsInformation();
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
            return $this->windows->processorsNumber();
        }
        else
        {
            echo microtime(true).PHP_EOL;
            return substr_count(file_get_contents('/proc/cpuinfo'), 'processor');
        }
    }

    protected function collectProcessorLoad() {
        if ($this->windows)
        {
            echo ($pl = microtime(true)).PHP_EOL;
            $load = $this->windows->processorLoad();
            //echo 'processor load: '.(microtime(true) - $pl).PHP_EOL;
            return $load;
        }
        else
        {
            echo microtime(true).PHP_EOL;
            exec('top -bn1 | head -n3', $p);
            $line = $p[2];
            preg_match('~([0-9.,]+) id~', $line, $load);
            return $load[1];
        }
    }

    protected function collectMemoryTotal() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            if ($this->memoryTotalCache === false)
                return ($this->memoryTotalCache = $this->windows->totalMemory());
            else
                return $this->memoryTotalCache;
        }
        else
        {
            echo microtime(true).PHP_EOL;
            $meminfo = file('/proc/meminfo');
            $line = explode(' ', $meminfo[0]);
            return $line[count($line) - 2];
        }
    }

    protected function collectMemoryFree() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->freeMemory();
        }
        else
        {
            echo microtime(true).PHP_EOL;
            $meminfo = file('/proc/meminfo');
            $line = explode(' ', $meminfo[1]);
            return $line[count($line) - 2];

        }
    }

    protected function collectSwapTotal() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->swapSize();
        }
        else
        {
            echo microtime(true).PHP_EOL;
            $meminfo = file('/proc/meminfo');
            foreach ($meminfo as $l)
            {
                $line = explode(' ', $l);
                if ($line[0] == 'SwapTotal:')
                    return $line[count($line) - 2];
            }
        }
    }

    protected function collectSwapFree() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->freeSwapSize();
        }
        else
        {
            echo microtime(true).PHP_EOL;
            $meminfo = file('/proc/meminfo');
            foreach ($meminfo as $l)
            {
                $line = explode(' ', $l);
                if ($line[0] == 'SwapFree:')
                    return $line[count($line) - 2];
            }

        }
    }

    protected function collectTasksNumber() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->tasksNumber();
        }
        else
        {
            echo microtime(true).PHP_EOL;
            exec('ps -aux', $p);
            return count($p) - 1;
        }
    }

    protected function collectTasksRunningNumber() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->runningTasksNumber();
        }
        else
        {
            echo microtime(true).PHP_EOL;
            exec('ps -auxr', $p);
            return count($p) - 1;
        }

    }

    protected function collectUptime() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->uptime();
        }
        else
        {
            echo microtime(true).PHP_EOL;
            exec('uptime --since', $o);
            $time = strtotime($o[0]);
            return time() - $time;
        }
    }
}
