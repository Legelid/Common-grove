<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;

class HolidayThemeService
{
    /**
     * Returns the active holiday definition for today, or null when no holiday is active.
     *
     * Priority (highest first): New Year > Christmas > Halloween > Autumn > Spring
     *
     * @return array{id:string,label:string,gradient:string,banner:array{phrase:string,bg:string,color:string}}|null
     */
    public function currentHoliday(): ?array
    {
        $md = (int) Carbon::now()->format('md'); // e.g. 1031 for Oct 31

        // New Year (Dec 31 – Jan 2)
        if ($md >= 1231 || $md <= 102) {
            return [
                'id'       => 'new_year',
                'label'    => 'New Year',
                'gradient' => 'radial-gradient(ellipse 70% 50% at 50% 100%, rgba(200,170,40,0.06) 0%, transparent 60%), radial-gradient(ellipse 50% 40% at 75% 0%, rgba(40,50,100,0.16) 0%, transparent 55%), #0D1117',
                'banner'   => ['phrase' => 'Happy New Year', 'bg' => 'rgba(160,130,20,0.07)', 'color' => '#C9A83C'],
            ];
        }

        // Christmas (Dec 15 – Dec 30)
        if ($md >= 1215 && $md <= 1230) {
            return [
                'id'       => 'christmas',
                'label'    => 'Christmas',
                'gradient' => 'radial-gradient(ellipse 70% 50% at 50% 100%, rgba(20,80,30,0.06) 0%, transparent 60%), radial-gradient(ellipse 50% 40% at 75% 0%, rgba(80,20,20,0.09) 0%, transparent 55%), #0D1117',
                'banner'   => ['phrase' => 'Happy Holidays', 'bg' => 'rgba(20,80,30,0.07)', 'color' => '#4A9E60'],
            ];
        }

        // Halloween (Oct 25 – Nov 1)
        if ($md >= 1025 && $md <= 1101) {
            return [
                'id'       => 'halloween',
                'label'    => 'Halloween',
                'gradient' => 'radial-gradient(ellipse 70% 50% at 50% 100%, rgba(160,60,0,0.06) 0%, transparent 60%), radial-gradient(ellipse 50% 40% at 75% 0%, rgba(60,0,90,0.08) 0%, transparent 55%), #0D1117',
                'banner'   => ['phrase' => 'Happy Halloween', 'bg' => 'rgba(140,50,0,0.08)', 'color' => '#C47A30'],
            ];
        }

        // Autumn (Sep 22 – Oct 24)
        if ($md >= 922 && $md <= 1024) {
            return [
                'id'       => 'autumn',
                'label'    => 'Autumn',
                'gradient' => 'radial-gradient(ellipse 70% 50% at 50% 100%, rgba(140,70,10,0.05) 0%, transparent 60%), radial-gradient(ellipse 50% 40% at 75% 0%, rgba(100,40,0,0.06) 0%, transparent 55%), #0D1117',
                'banner'   => ['phrase' => 'Enjoy the autumn', 'bg' => 'rgba(120,60,10,0.07)', 'color' => '#B87840'],
            ];
        }

        // Spring (Mar 20 – Jun 20)
        if ($md >= 320 && $md <= 620) {
            return [
                'id'       => 'spring',
                'label'    => 'Spring',
                'gradient' => 'radial-gradient(ellipse 70% 50% at 50% 100%, rgba(20,100,40,0.05) 0%, transparent 60%), radial-gradient(ellipse 50% 40% at 75% 0%, rgba(60,120,60,0.06) 0%, transparent 55%), #0D1117',
                'banner'   => ['phrase' => 'Happy spring', 'bg' => 'rgba(40,100,50,0.06)', 'color' => '#5A9E6A', 'show' => false],
            ];
        }

        return null;
    }
}
