<?php

namespace App\Repository\Interfaces;

use App\Entity\Interfaces\InquiryInterface;

interface InquiryRepositoryInterface
{
    /**
     * 連続送信チェック
     * @param InquiryInterface $inquiry
     * @param string $thresholdTime
     * @return bool
     */
    public function isContinuePost(InquiryInterface $inquiry, string $thresholdTime = "-2 minute"): bool;
}