<?php
/* @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile

namespace Illuminate\Support {
    class Carbon {
        ///**
        // * @see \NormanHuth\Library\Support\Macros\Carbon\ToAppTimezoneMacro::__invoke()
        // */
        //public function toAppTimezone(): \Illuminate\Support\Carbon
        //{
        //    /** @var \Illuminate\Support\Carbon $instance */
        //    if ($timezone = config('app.public_timezone')) {
        //        return $instance->tz($timezone);
        //    }
        //
        //    return $instance;
        //}

        ///**
        // * @see \NormanHuth\Library\Support\Macros\Carbon\ResolveTimezoneMacro::__invoke()
        // */
        //public function resolveTimezone(\Illuminate\Http\Request $request): \Illuminate\Support\Carbon
        //{
        //    /** @var \Illuminate\Support\Carbon $instance */
        //    if ($timezone = $request->session()?->get('timezone')) {
        //        return $instance->tz($timezone);
        //    }
        //    if ($timezone = $request->user()?->timezone) {
        //        return $instance->tz($timezone);
        //    }
        //    if ($timezone = config('app.public_timezone')) {
        //        return $instance->tz($timezone);
        //    }
        //
        //    return $instance;
        //}

        ///**
        // * @see \NormanHuth\Library\Support\Macros\Carbon\ToSessionTimezoneMacro::__invoke()
        // */
        //public function toSessionTimezone(\Illuminate\Http\Request $request): \Illuminate\Support\Carbon
        //{
        //    /** @var \Illuminate\Support\Carbon $instance */
        //    if ($timezone = $request->session()?->get('timezone')) {
        //        return $instance->tz($timezone);
        //    }
        //
        //    return $instance;
        //}

        /**
         * @see \NormanHuth\Library\Support\Macros\Carbon\ToUserTimezoneMacro::__invoke()
         */
        public function toUserTimezone(\Illuminate\Http\Request $request): \Illuminate\Support\Carbon|\Carbon\Carbon
        {
            /** @var \Illuminate\Support\Carbon $instance */
            if ($instance = $request->user()?->timezone) {
                return $this->tz($timezone);
            }

            return $instance;
        }

        /**
         * @see \App\Providers\AppServiceProvider::macros()
         */
        public function publicFormatted(\Illuminate\Http\Request $request): string
        {
            /** @var \Illuminate\Support\Carbon|\Carbon\Carbon $instance */
            return $instance->toUserTimezone($request)->translatedFormat('M j');
        }
    }
}
