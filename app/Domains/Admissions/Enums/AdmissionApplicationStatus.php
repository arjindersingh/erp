<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Enums;

enum AdmissionApplicationStatus: string
{
    case Draft = 'draft';
    case DataEntryInProgress = 'data_entry_in_progress';
    case Submitted = 'submitted';
    case UnderCompletenessCheck = 'under_completeness_check';
    case ReturnedForCorrection = 'returned_for_correction';
    case DocumentsPending = 'documents_pending';
    case UnderDocumentScrutiny = 'under_document_scrutiny';
    case DocumentsVerified = 'documents_verified';
    case EligibilityPending = 'eligibility_pending';
    case Eligible = 'eligible';
    case ConditionallyEligible = 'conditionally_eligible';
    case NotEligible = 'not_eligible';
    case AssessmentPending = 'assessment_pending';
    case AssessmentInProgress = 'assessment_in_progress';
    case AssessmentCompleted = 'assessment_completed';
    case MeritPending = 'merit_pending';
    case MeritGenerated = 'merit_generated';
    case Waitlisted = 'waitlisted';
    case Selected = 'selected';
    case OfferIssued = 'offer_issued';
    case OfferAccepted = 'offer_accepted';
    case OfferDeclined = 'offer_declined';
    case OfferExpired = 'offer_expired';
    case FinalVerificationPending = 'final_verification_pending';
    case FinallyVerified = 'finally_verified';
    case AdmissionConfirmed = 'admission_confirmed';
    case Converted = 'converted';
    case Withdrawn = 'withdrawn';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';
    case Expired = 'expired';
}
