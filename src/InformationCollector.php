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
    }

    protected function collectProcessorLoad() {
        if ($this->windows)
        {
            echo ($pl = microtime(true)).PHP_EOL;
            $load = $this->windows->processorLoad();
            //echo 'processor load: '.(microtime(true) - $pl).PHP_EOL;
            return $load;
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
    }

    protected function collectMemoryFree() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->freeMemory();
        }
    }

    protected function collectSwapTotal() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->swapSize();
        }
    }

    protected function collectSwapFree() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->freeSwapSize();
        }
    }

    protected function collectTasksNumber() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->tasksNumber();
        }
    }

    protected function collectTasksRunningNumber() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->runningTasksNumber();
        }
    }

    protected function collectUptime() {
        if ($this->windows)
        {
            echo microtime(true).PHP_EOL;
            return $this->windows->uptime();
        }
    }
}
