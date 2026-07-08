<?php

namespace Plugin\SubcontractInstall;

final class Config
{
    public const API_BASE_URL = env('url');
    public const API_KEY = env('MI_API_KEY');
    public const TIMEOUT = 10;
    public const VERIFY_SSL = false;
}
