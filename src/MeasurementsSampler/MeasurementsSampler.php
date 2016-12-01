<?php
/** Freesewing\MeasurementsSampler class */
namespace Freesewing;

/**
 * Samples a pattern for a group for models
 *
 * This takes a group of models (with different measurements) and generates
 * the pattern parts for them, aligning them properly.
 * This allows you to verify that your pattern grades nicely over a range of 
 * sizes/measurements.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class MeasurementsSampler extends Sampler
{

    /**
     * Loads models for which we'll sampler the pattern.
     *
     * @param string group Name of the model group as defined in sampler config
     *
     * @return array|null Array of models or null if we can't read the config file
     *
     * @throws InvalidArgumentException if the config file cannot be read
     */
    public function loadPatternModels($group)
    {
        $config = $this->getSamplerModelsFile($this->pattern);
        if (is_readable($config)) {
            $this->measurementsConfig = \Freesewing\Yamlr::loadYamlFile($config);
            $this->models = $this->loadModelGroup($this->modelGroupToLoad($group));

            return $this->models;
        } else {
            throw new \InvalidArgumentException("Could not read sampler configuration");
        }
    }

    /**
     * Samples the pattern for each model (set of measurements)
     *
     * For each model, this clones the pattern and calls the sample() method
     * with the model as parameter.
     * It then itterates over the parts and calls sampleParts() on them
     *
     * @param \Freesewing\Theme or similar
     *
     * @return \Freesewing\Pattern or similar
     */
    public function sampleMeasurements($theme)
    {
        $renderBot = new \Freesewing\SvgRenderbot();
        $steps = count($this->models);
        $i = 0;
        foreach ($this->models as $modelKey => $model) {
            $p = clone $this->pattern;
            $p->loadParts();
            $p->sample($model);
            foreach ($p->parts as $partKey => $part) {
                $this->sampleParts($i, $steps, $p, $theme, $renderBot);
            }
            ++$i;
        }
        $this->addSampledPartsToPattern();
        $theme->applyRenderMask($this->pattern);
        $this->pattern->layout();
        return $this->pattern;
    }

    /**
     * Loads models from the sampler config file.
     *
     * @param string group Name of the model group as defined in sampler config
     *
     * @return array Array of models
     */
    private function loadModelGroup($group)
    {
        foreach ($this->measurementsConfig['groups'][$group] as $member) {
            $model = new \Freesewing\Model();
            $measurements = array_combine(array_keys($this->pattern->config['measurements']), $this->measurementsConfig['measurements'][$member]);
            $model->addMeasurements($measurements);
            $models[$member] = $model;
        }

        return $models;
    }

    /**
     * Figures out what model group to load.
     *
     * This checks whether the passed argument is a group
     * It it is, it returns it.
     * If not, it returns the default group
     *
     * @param string group Name of the model group to look for
     *
     * @return string Name of the group to load
     */
    private function modelGroupToLoad($group)
    {
        if (is_array($this->measurementsConfig['groups'][$group])) {
            return $group;
        } else {
            return $this->measurementsConfig['default']['group'];
        }
    }
}
