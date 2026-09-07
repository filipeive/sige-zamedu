<?php

namespace App\Services;

class SchoolContext
{
    /**
     * @var int|null Current active school ID
     */
    protected static ?int $schoolId = null;

    /**
     * Set the current active school ID context.
     *
     * @param int|null $schoolId
     * @return void
     */
    public static function setSchoolId(?int $schoolId): void
    {
        static::$schoolId = $schoolId;
    }

    /**
     * Get the current active school ID context.
     *
     * @return int|null
     */
    public static function getSchoolId(): ?int
    {
        return static::$schoolId;
    }

    /**
     * Check if an active school ID context is set and valid.
     *
     * @return bool
     */
    public static function hasSchoolContext(): bool
    {
        if (is_null(static::$schoolId)) {
            return false;
        }

        if (!\App\Models\School::where('id', static::$schoolId)->exists()) {
            static::$schoolId = null;
            return false;
        }

        return true;
    }

    /**
     * Clear the active school context.
     *
     * @return void
     */
    public static function clear(): void
    {
        static::$schoolId = null;
    }

    /**
     * Execute a callback within a given school context, then restore original context.
     *
     * @param int|null $schoolId
     * @param callable $callback
     * @return mixed
     */
    public static function runInContext(?int $schoolId, callable $callback)
    {
        $previousSchoolId = static::$schoolId;
        
        try {
            static::setSchoolId($schoolId);
            return $callback();
        } finally {
            static::setSchoolId($previousSchoolId);
        }
    }
}
