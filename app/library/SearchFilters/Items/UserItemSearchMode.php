<?php

namespace Apprecie\Library\SearchFilters\Items;

use Apprecie\Library\Collections\Enum;

class UserItemSearchMode extends Enum
{
    const CONFIRMED = 'confirmed';
    const BY_ARRANGEMENT = 'byarrangement';
    const BOTH = 'both';
}