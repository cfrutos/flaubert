<?php
namespace Flaubert\Persistence\Elastic\Normalization;

abstract class SpecificNormalizer extends AbstractNormalizer
{
    /**
     * Class of objects to be normalized
     *
     * @var string
     */
    protected $class;

    public function __construct()
    {
        parent::__construct();

        $this->init();
    }

    /**
     * @param string $class
     *
     * @return self
     */
    protected function setClass($class)
    {
        $this->class = (string) $class;

        return $this;
    }

    /**
     * Intialization
     *
     * @return void
     */
    protected abstract function init();

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_a($data, $this->class);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return
            ($type === $this->class) ||
            is_subclass_of($type, $this->class) ||
            in_array($type, class_implements($type));
    }
}