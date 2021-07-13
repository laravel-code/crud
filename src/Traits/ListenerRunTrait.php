<?php

namespace LaravelCode\Crud\Traits;

trait ListenerRunTrait
{
    /**
     * @param bool $value
     */
    protected function setDelete(bool $value = false): void
    {
        $this->delete = $value;
    }

    /**
     * @param bool $value
     */
    protected function setRestore(bool $value = false): void
    {
        $this->restore = $value;
    }

    /**
     * @param string $field
     * @param $value
     */
    public function __set(string $field, $value)
    {
        $this->entity->{$field} = $value;
    }

    /**
     * @param string $field
     * @return mixed | null
     */
    public function __get(string $field)
    {
        if (! $this->entity) {
            return;
        }

        return $this->entity->{$field};
    }

    /**
     * @param $field
     * @param $value
     */
    protected function setField($field, $value)
    {
        $this->entity->{$field} = $value;
    }

    protected function setModel()
    {
        return false;
    }

    /**
     * Should the entity save be called when the entity is clean.
     * By default it will not, but when you have could that is
     * being executed e.g. beforeSave, afterSave and afterSaveFailed.
     *
     * return true to continue handling the saving.
     *
     * @return bool
     */
    protected function saveOnClean()
    {
        return false;
    }

    protected function beforeRun()
    {
    }

    protected function afterRun()
    {
    }

    protected function beforeSave()
    {
    }

    protected function afterSave()
    {
    }

    protected function afterSaveFailed()
    {
    }

    protected function afterDelete()
    {
    }

    protected function afterRestore()
    {
    }
}
