<?php
declare(strict_types=1);

namespace App\Models\Service\Currency;

use App\Currency;

/**
 * Returns currency courses list and caches them
 */
class GetCourses
{
    private const KEY_BY_SIGN = 'by_sign';
    private const KEY_BY_CODE = 'by_code';

    /**
     * @var array
     */
    private $courses;

    /**
     * @return float[]
     */
    public function bySign(): array
    {
        $courses = $this->getCourses();
        $result = $courses[self::KEY_BY_SIGN] ?? [];

        return $result;
    }

    /**
     * @return float[]
     */
    public function byCode(): array
    {
        $courses = $this->getCourses();
        $result = $courses[self::KEY_BY_CODE] ?? [];

        return $result;
    }

    /**
     * @return array
     */
    private function getCourses(): array
    {
        if (null === $this->courses) {
            $this->courses = [];
            $currencies = Currency::all(['sign', 'course']);

            foreach ($currencies as $currency) {
                $sign = $currency->sign;
                $code = $currency->code;
                $course = $currency->course;

                $this->courses[self::KEY_BY_SIGN][$sign] = $course;
                $this->courses[self::KEY_BY_CODE][$code] = $course;
            }
        }

        return $this->courses;
    }
}
