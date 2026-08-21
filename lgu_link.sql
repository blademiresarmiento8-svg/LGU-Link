-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2026 at 06:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lgu_link`
--

-- --------------------------------------------------------

--
-- Table structure for table `citizens_charter_services`
--

CREATE TABLE `citizens_charter_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `office` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `keywords` text NOT NULL,
  `requirements` text NOT NULL,
  `fee` varchar(255) NOT NULL,
  `processing_time` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `citizens_charter_services`
--

INSERT INTO `citizens_charter_services` (`id`, `office`, `title`, `keywords`, `requirements`, `fee`, `processing_time`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Business Permits & Licensing Office', 'Issuance of Business Permit (New)', 'new business permit\nbusiness permit\nmayor\'s permit\nmayors permit\nmayor\'s clearance\nmayors clearance\nstart a business\nregister a business\nopen a business', 'Community Tax Certificate / CTC (Municipal Treasurer\'s Office)\nBarangay Business Clearance for the current year\nProof of registration: DTI (sole proprietorship), SEC (partnership/corporation), or CDA (cooperative)\nProof of right to use the property as business location (TCT/Tax Declaration if owned; notarized Lease Contract or Affidavit to Use if not)\nCertificate of Non-Coverage (CNC) and clearances from MENRO, MPDO (zoning), Engineering (occupancy), Rural Health Office (sanitary), and BFP (fire safety) as applicable\nSpecial document requirements for regulated businesses (e.g. FDA license for pharmacies, DOLE/POEA for agencies) — ask BPLO if yours applies', 'Varies by clearance — e.g. CNC around ₱1,000, plus MPDO/BFP assessment based on your business', 'About 1 day and 45 minutes once all requirements are complete', 0, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(2, 'Business Permits & Licensing Office', 'Renewal of Business Permit', 'renew business permit\nbusiness permit renewal\nrenewal of business permit', 'Community Tax Certificate / CTC\nBarangay Business Clearance for the current year\nProof of registration (DTI/SEC/CDA)\nPrevious year\'s Mayor\'s Permit\nProof of tax payments (BIR) or notarized Affidavit of Declaration of Gross Income\nCertificate of tax exemption, if your business is tax-exempt', 'Based on assessed gross sales/receipts', 'About 1 day and 45 minutes', 1, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(3, 'Business Permits & Licensing Office', 'Motorized Tricycle Operator\'s Permit (MTOP)', 'tricycle permit\nmtop\nmotorized tricycle', 'Barangay Clearance for the current year\nPolice Clearance\nCertification from the TODA President and the Federation President\nOfficial Receipt (OR) and Certificate of Registration (CR) of the tricycle\nProfessional Driver\'s License\nDeed of Sale / Sales Invoice\nVoter\'s ID or Certified True Copy of Voter Registration (VRR)', '₱575 for new, ₱210 for renewal', 'About 1 day and 30 minutes', 2, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(4, 'Municipal Assessor\'s Office', 'Transfer of Tax Declaration to New Owner/s', 'transfer tax declaration\ntransfer of ownership property\nchange owner tax declaration', 'New title (if titled) or Deed of Conveyance (Sale, Donation, or Extrajudicial Settlement)\nBIR Certification (Capital Gains Tax, Donor\'s Tax, or Estate Tax)\nTransfer Tax receipt (Provincial Treasurer\'s Office)\nLatest/updated Real Property Tax Receipt\nIf applicable: subdivision/consolidation plan, Affidavit of Publication (inherited property), DAR clearance, SPA if represented by someone else', '₱200 per Tax Declaration', 'About 1 hour', 3, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(5, 'Municipal Assessor\'s Office', 'Consolidation and Subdivision of Property', 'consolidation of property\nsubdivision of land\nsubdivide property\nmerge lots\nmerge parcels', 'Title/s (if titled)\nApproved subdivision/consolidation plan (Bureau of Lands)\nLatest/updated Real Property Tax Receipt', '₱200 per Tax Declaration', 'About 1 hour', 4, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(6, 'Municipal Assessor\'s Office', 'Demolition and Cancellation of Property Record', 'demolition of property\ncancel tax declaration\ncancellation of property record', 'Written request\nLatest/updated Real Property Tax Receipt\nOcular inspection report (conducted by the Assessor\'s Office)\nDemolition Permit (Engineering Office) and/or Certification of Business Closure (BPLO), if applicable', 'None', 'About 2 hours 31 minutes (includes the ocular inspection)', 5, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(7, 'Municipal Assessor\'s Office', 'Reassessment / Assessment of Property', 'reassessment of property\nassess new property\ndeclare new property for tax', 'Written request\nLatest/updated Real Property Tax Receipt\nOcular inspection report', '₱200 per Tax Declaration', 'About 2 hours 56 minutes (includes ocular inspection, 2–8 hours depending on location)', 6, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(8, 'Municipal Assessor\'s Office', 'Reclassification of Property', 'reclassification of property\nreclassify land', 'Title/s\nSangguniang Bayan Resolution\nSangguniang Panlalawigan Resolution\nLatest/updated Real Property Tax Receipt', '₱200 per Tax Declaration', 'About 1 hour', 7, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(9, 'Municipal Assessor\'s Office', 'Tax Declaration for Newly Assessed Land, Building, or Machinery', 'new tax declaration\ntax declaration for new building\nnewly assessed property\ndeclare new machinery', 'For land: existing Tax Declaration\nFor a building: Building Permit, Architectural Plan, Cost Estimate, Certificate of Occupancy\nFor machinery: Notarized Sworn Statement, plus SPA if represented', '₱200 per Tax Declaration', 'About 2 hours 56 minutes', 8, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(10, 'Municipal Assessor\'s Office', 'Certified True Copy of Tax Declaration', 'certified true copy of tax declaration\ncopy of tax declaration', 'Copy of Tax Declaration\nLatest/updated Real Property Tax Receipt\nLetter request from the owner or authorized representative (with SPA if represented)', '₱100 per Tax Declaration', 'About 25 minutes', 9, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(11, 'Municipal Assessor\'s Office', 'Property Certifications (With/Without Improvement, Land Holdings, No Property)', 'certificate of no property\ncertificate of land holdings\ncertificate with improvement\ncertificate non improvement\nindigency property certificate', 'Copy of Tax Declaration\nLatest/updated Real Property Tax Receipt\nLetter request from the owner or authorized representative (with SPA if represented)', '₱100 per certification', 'About 25 minutes', 10, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(12, 'Municipal Civil Registrar\'s Office', 'Certified True Copy of Birth / Death / Marriage Certificate', 'certified true copy of birth certificate\ncopy of birth certificate\ncopy of death certificate\ncopy of marriage certificate\nform 1a\nform 2a\nform 3a', 'Accomplished request slip\n1 photocopy of 2 valid IDs\nIf through a representative: original authorization letter or SPA, plus valid ID of both the owner and the representative', 'About ₱200 total (verification fee + copy fee)', 'About 12 minutes', 11, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(13, 'Municipal Civil Registrar\'s Office', 'Registration of Birth, Death, or Marriage', 'register a birth\nregister a death\nregister a marriage\nbirth registration\ndeath registration\nmarriage registration', 'Duly accomplished Certificate of Live Birth/Death/Marriage (4 original copies, from the hospital/officiant)\nResidence Certificate (Cedula) of parents, if not married\nValid ID of the informant\nMarriage Certificate of parents, if applicable\nAffidavit of Paternity and Affidavit to Use the Surname of the Father, if not married', 'About ₱650 total (birth ₱100, marriage ₱100, burial ₱50, misc ₱100, cadaver transfer ₱200 — as applicable)', 'About 36 minutes', 12, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(14, 'Municipal Civil Registrar\'s Office', 'Application for Marriage License', 'marriage license\napply for marriage license', 'PSA CENOMAR (original)\nBirth Certificate\nResidence Certificate (Cedula)\nIf 18–20 years old: parental consent signed by the father at MCRO, plus the father\'s valid ID and Cedula', 'About ₱400 total', 'About 36 minutes to process, but the license can only be released after a mandatory 10-calendar-day posting period', 13, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(15, 'Municipal Civil Registrar\'s Office', 'Late Registration / Out-of-Town Registration of Birth, Death, or Marriage', 'late registration of birth\ndelayed registration\nout of town registration', '4 copies of duly accomplished Certificate of Live Birth (or the Death/Marriage equivalent)\nAffidavit of Delayed Registration\nNegative Certification from PSA\nTwo documentary evidence plus Affidavit of Two Disinterested Persons\nNational ID of the registrant and parents, Barangay Certification of residency, 2 recent photos\nAdditional affidavits depending on your case (paternity, whereabouts of mother, etc.)', '₱150', 'About 21 minutes to process, then a required 10-calendar-day posting period before release', 14, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(16, 'Municipal Civil Registrar\'s Office', 'Supplemental Report (correcting an omitted entry)', 'supplemental report birth certificate\nmissing entry birth certificate', 'PSA Birth Certificate and local copy\nAffidavit of Supplemental Report (valid for 1 month)\nBaptismal Certificate, School Record, 2 valid IDs\nBirth/Marriage Certificates of parents', '₱150', 'About 1 month (the annotated copy is released by PSA after 1 month)', 15, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(17, 'Municipal Civil Registrar\'s Office', 'RA 9255 — Affidavit to Use the Surname of the Father (AUSF)', 'use surname of father\nra 9255\nausf', 'Affidavit of Acknowledgment/Admission of Paternity\nBirth Certificate of the child\nAUSF form (with the mother\'s sworn attestation if the child is 7–20 years old)\nValid IDs of parents and child', '₱150', 'About 1 month (annotated document released by PSA)', 16, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(18, 'Municipal Civil Registrar\'s Office', 'Legitimation', 'legitimation of child\nlegitimate a child\nlegitimation', 'Affidavit of Legitimation\nBirth Certificate of the child (PSA and local copy)\nMarriage Certificate of parents, Baptismal Certificate\nPSA Advisory on Marriage (both parents), valid IDs', '₱150', 'About 1 month', 17, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(19, 'Municipal Civil Registrar\'s Office', 'Petition for Correction — RA 10172 (Gender / Date / Month of Birth)', 'ra 10172\ncorrect gender\ncorrect date of birth\ncorrect month of birth', 'PSA and local copy of the Birth/Death/Marriage Certificate\nBaptismal Certificate, Medical Record/Certificate\nNBI and Police Clearance, Affidavit of Employment/Non-Employment\nForm 137/Transcript, Cedula, Voter\'s Certification, 2 valid IDs\nNewspaper publication (you shoulder roughly ₱1,500 publisher fee)\nFull checklist can vary by case — MCRO will confirm exactly what applies to you', '₱3,000 plus about ₱1,500 for required newspaper publication', 'About 4 months (includes mandatory 10-day posting and PSA review)', 18, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(20, 'Municipal Civil Registrar\'s Office', 'RA 9048 — Correction of Clerical Error', 'ra 9048\nclerical error correction\nmisspelled name correction', 'PSA and local copy of the Birth/Death/Marriage Certificate\nBaptismal Certificate, Marriage Contract\nBirth Certificates of siblings/children, Cedula, Voter\'s Registration, 2 valid IDs\nFull checklist can vary by case — MCRO will confirm exactly what applies to you', '₱1,000', 'About 50 days (includes posting and PSA review)', 19, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(21, 'Municipal Civil Registrar\'s Office', 'RA 9048 — Petition for Change of First Name', 'change of first name\npetition for change of name', 'PSA and local copy of Birth Certificate, Baptismal Certificate\nMarriage Contract, NBI and Police Clearance\nAffidavit of Employment/Non-Employment, Form 137/Diploma, Cedula\nNewspaper publication, Voter\'s Certification, 2 valid IDs', '₱3,000 plus about ₱1,500 for required newspaper publication', 'About 4 months', 20, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(22, 'Municipal Civil Registrar\'s Office', 'Endorsement of Negative Certification / Clear Copy / Advance Copy', 'negative certification\nadvance copy of birth certificate\nclear copy endorsement', 'PSA Negative Certification\nProof of urgency, 1 valid ID\nCertificate of Live Birth (Municipal Form)', '₱100–150', 'About 31 days', 21, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(23, 'Municipal Engineering Office', 'Building Permit', 'building permit\nconstruct a building\nconstruction permit', 'Barangay Clearance for Building Permit\nProof of Lot Ownership\nLocational/Zoning Clearance (MPDO)\nFire Safety Evaluation Clearance (BFP)\nConstruction Safety & Health Program (DOLE)\n5 sets of survey plans, design plans, and related documents\nClearances from other agencies if needed (DPWH, DepEd, CAAP, DENR, DOH, etc.)\nApply online: bldg.ibpls.com/norzagaraybulacan', 'Based on the National Building Code fee schedule and local ordinances', 'About 1 day and 1 hour 22 minutes', 22, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(24, 'Municipal Engineering Office', 'Certificate of Occupancy', 'certificate of occupancy\noccupancy certificate\noccupancy permit', 'Issued Building Permit and Fire Safety/Locational Clearance\n3 notarized copies of Certificate of Completion\nAs-Built Plan and Construction Logbook (signed & sealed)\nCaptioned site photos of the completed building\nYellow card from your electrical source provider (e.g. Meralco)\nApply online: co.ibpls.com/norzagaraybulacan', 'Based on the National Building Code fee schedule', 'About 1 day and 53 minutes', 23, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(25, 'Municipal Engineering Office', 'Certificate of Electrical Inspection and Wiring Permit', 'electrical inspection\nwiring permit', 'Proof of lot ownership, Barangay Clearance for Electrical/Wiring\nIssued Building Permit, 2 valid IDs\nWiring Permit signed by a licensed professional or registered electrical engineer', 'Based on the National Building Code fee schedule', 'About 2 hours 30 minutes (includes ocular inspection)', 24, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(26, 'Municipal Health Office', 'Medical Consultation', 'medical consultation\nsee a doctor\ncheckup at health center', 'Your physical presence\nPrevious medical record related to your condition, if you have one', 'Free', 'About 20–40 minutes, depending on whether lab tests are needed', 25, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(27, 'Municipal Health Office', 'Dental Consultation', 'dental consultation\ntooth extraction\nsee a dentist', 'Your physical presence\nPrevious dental record, if applicable', 'Free', 'About 65 minutes', 26, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(28, 'Municipal Health Office', 'Maternal Health Care Services', 'maternal health\nprenatal checkup\npregnancy checkup', 'Your physical presence\nMaternal & child booklet, if you have one', 'Free', 'About 35 minutes', 27, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(29, 'Municipal Health Office', 'National Immunization Program', 'immunization\nvaccination\nvaccine schedule', 'Your physical presence\nPrevious vaccination record, if applicable', 'Free', 'About 20 minutes', 28, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(30, 'Municipal Health Office', 'Family Planning Services', 'family planning', 'Your physical presence\nPrevious record, if applicable', 'Free', 'About 20 minutes', 29, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(31, 'Municipal Health Office', 'Processing/Issuance of Death Certificate & Exhumation Permit', 'exhumation permit\nprocess death certificate', 'Filled-out Death Certificate\nOfficial medical records (from within 10 years before death)\nOriginal copy of death certificate, for exhumation', '₱200', 'About 10 minutes', 30, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(32, 'Municipal Health Office', 'Health Card for Employment', 'health card\nhealth card for employment', 'Medical laboratory result (from a diagnostic laboratory, clinic, or hospital)', '₱100', 'About 15 minutes', 31, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(33, 'Municipal Health Office', 'Sanitary Permit', 'sanitary permit', 'ECC / Discharge Permit / Permit to Operate as applicable\nApplication Permit\nMedical results of employees, if necessary\nWater Test Results, for water refilling stations', '₱300–₱2,550 depending on the business and whether inspection is needed', 'About 5 minutes for simple cases, up to 1 day if inspection is required', 32, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(34, 'Municipal Health Office', 'Potability Certificate', 'potability certificate\nwater potability', 'Water Test Results from a DOH-accredited laboratory', '₱100', 'About 5 minutes', 33, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(35, 'Municipal Planning & Development Office', 'Zoning Certification for Business Permit — Non-Critical Projects (New)', 'zoning certification business\nzoning clearance business', 'Proof of property ownership/lease\nDTI/SEC/CDA registration\nUpdated Barangay Clearance\nSketch of location, actual project photo\nCertificate of Non-Coverage (CNC)\nLatest tax receipt', '₱500 per hectare', 'About 33 minutes', 34, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(36, 'Municipal Planning & Development Office', 'Zoning Certification for Business Permit — Critical Projects (New)', 'zoning certification critical project', 'Same as the non-critical checklist, but with an Environmental Compliance Certificate (ECC) instead of a CNC\nA site inspection is also required before approval', '₱500 per hectare', 'About 53 minutes, plus a site inspection (subject to inspector availability, up to 48 hours)', 35, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(37, 'Municipal Planning & Development Office', 'Zoning Certification for Renewal of Business Permit', 'zoning certification renewal', 'Photocopy of Business Permit\nUpdated Barangay Clearance\nLatest tax receipt\nActual photos', 'Minimal, assessed on-site', 'About 33 minutes', 36, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(38, 'Municipal Planning & Development Office', 'Zoning Certification (as to Land Use)', 'zoning certification land use\nland use certification', 'Transfer Certificate of Title\nTax Declaration with latest tax receipt\nSketch of location', '₱500 per hectare', 'About 29 minutes', 37, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(39, 'Municipal Planning & Development Office', 'Locational Clearance for Building Permit — Critical Projects', 'locational clearance critical project\nlocational clearance', 'Transfer Certificate of Title\nDTI/SEC/CDA registration\nUpdated Barangay Clearance\nSketch of location\nEnvironmental Compliance Certificate (ECC)\nLatest tax receipt\nArchitectural/Site Development Plan with bill of materials\nSupporting affidavits as applicable', 'Based on the Revenue Code and the project\'s bill of materials', 'About 35 minutes, plus a site inspection', 38, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(40, 'Municipal Environment & Natural Resources Office (MENRO)', 'Environmental Clearance for Business Permit — Non-Critical (New)', 'environmental clearance non critical\nenvironmental clearance new business', 'Application form\nCertificate of Non-Coverage (apply online via EMB-DENR)\nBarangay Business Clearance\nDTI/SEC/CDA registration\nGeotagged photo of the project/business\nValid government ID', 'Plus environmental inspection and solid waste management fees assessed per project', 'About 12 minutes', 39, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(41, 'Municipal Environment & Natural Resources Office (MENRO)', 'Environmental Clearance for Business Permit — Non-Critical (Renewal)', 'environmental clearance renewal', 'Application form\nLocal Certificate of Non-Coverage for renewal, plus its Official Receipt\nBarangay Business Clearance\nDTI/SEC/CDA registration', '₱1,000', 'About 10 minutes', 40, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(42, 'Municipal Environment & Natural Resources Office (MENRO)', 'Environmental Clearance for Business Permit — Critical Projects/Areas', 'environmental clearance critical project\nenvironmental clearance critical area', 'Environmental Compliance Certificate (ECC)\nProvincial Government Acknowledgement Certificate\nBarangay Business Clearance\nDTI/SEC registration\nValid ID\nRenewals also need PCO Accreditation, Permit to Operate, and Wastewater Discharge Permit', 'Plus environmental, solid waste, and pollution hazard fees assessed per project', 'About 20 minutes', 41, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(43, 'Municipal Environment & Natural Resources Office (MENRO)', 'Certificate of No Objection for Tree Cutting Permit', 'tree cutting permit\ncertificate of no objection tree\ncut down a tree', '2 request/application letters (addressed to CENRO-DENR and the Municipal Mayor)\nAuthenticated land title or proof of ownership\nSketch map\nBarangay Endorsement (no objection)\nGeotagged photos of the trees\nValid government ID\nChainsaw registration (CENRO-DENR)\nDENR clearance that the area is not forestland/protected', '₱300 per tree + ₱100 certification fee + ₱1,000 environmental inspection fee', 'About 2 days (includes a joint ocular inspection with DENR)', 42, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(44, 'Municipal Social Welfare & Development Office (MSWDO)', 'Assistance to Individuals in Crisis Situation (AICS)', 'aics\nfinancial assistance\nmedical assistance\nburial assistance\neducational assistance\ncrisis assistance', 'Barangay Certificate of Indigency\nLetter of request addressed to the Municipal Mayor\nValid IDs of patient/claimant\nProof of need: hospital bill/medical certificate (medical), Certificate of Registration & Statement of Account (educational), or funeral contract & death certificate (burial)', 'Free — this is a benefit, not a paid service', 'About 14 minutes', 43, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(45, 'Municipal Social Welfare & Development Office (MSWDO)', 'MSWDO Certification (e.g. Guardianship)', 'guardianship certification\nmswdo certification', 'Barangay Certificate of Indigency\nFor guardianship: birth certificate of children, guardian\'s valid ID, notarized Affidavit of Guardianship, barangay certification of the minor\'s circumstances, death certificate of parents (if applicable)', 'Free', 'About 12 minutes', 44, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(46, 'Municipal Social Welfare & Development Office (MSWDO)', 'Social Case Study Report (SCSR)', 'social case study report\nscsr', 'Valid ID of client and the family member processing the SCSR\nCase reference (medical protocol, legal protocol, or other supporting document)', 'Free', 'About 10 days (includes a home visit)', 45, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(47, 'Municipal Social Welfare & Development Office (MSWDO)', 'Adoption', 'adoption\nadopt a child', 'Notarized Petition for Adoption\nHome Study Report / Case Study Report\nBirth and marriage certificates of PAPs (SECPA copy)\nNBI/police clearance, medical & psychological evaluations\nChild care plan, character references, proof of financial capacity\nAttendance at a pre-adoption seminar', 'Free', 'About 37 days (Home Study Report preparation alone takes about 30 working days)', 46, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(48, 'Municipal Social Welfare & Development Office (MSWDO)', 'Person With Disability (PWD) ID', 'pwd id\ndisability id\nperson with disability id', 'Medical Certificate stating the disability\nCertificate of Residency\n2 pcs 1x1 ID picture', 'Free', 'About 7 minutes', 47, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(49, 'Municipal Social Welfare & Development Office (MSWDO)', 'Solo Parent\'s ID', 'solo parent id\nsolo parent', 'Sworn Affidavit\nBirth certificate of minor children\nCertificate of Attendance at the RA 11861 orientation (new applicants)\n2 pcs 1x1 ID picture\nProof of income', 'Free', 'About 7 days (includes a home visit)', 48, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(50, 'Municipal Social Welfare & Development Office (MSWDO)', 'Senior Citizen\'s ID', 'senior citizen id\nsenior citizen', 'Birth Certificate\nCertificate of Residency\n2 pcs 1x1 ID picture', 'Free', 'About 7 minutes', 49, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(51, 'Municipal Social Welfare & Development Office (MSWDO)', 'Livelihood Assistance', 'livelihood assistance\ncapital assistance\nbusiness capital assistance', 'Barangay Certificate of Indigency\nApplication Form\nLetter of request addressed to the Municipal Mayor\nPhotocopy of ID\nPhoto of yourself with your livelihood project/site\nList of products or materials needed with prices\nSketch of your house location', 'Free — this is interest-free capital assistance', 'About 7 days (includes a home visit)', 50, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(52, 'Municipal Social Welfare & Development Office (MSWDO)', 'Provision of Assistive Devices', 'assistive device\nwheelchair request\nassistive devices', 'Barangay Certificate of Indigency\nLetter of request addressed to the Municipal Mayor\nValid IDs of patient/claimant\nMedical Certificate\nPhoto of the patient requesting the device', 'Free', 'About 33 minutes', 51, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(53, 'Municipal Treasurer\'s Office', 'Real Property Tax (RPT) Receipt / Payment', 'real property tax\npay amilyar\nrpt payment\nproperty tax payment', 'Previous RPT Official Receipt, latest Tax Declaration, or Title\nValid government-issued ID\nFor corporations: letter of request plus SPA if represented', '1% of assessed value (Basic RPT) + 1% of assessed value (Special Education Fund), plus penalties or less discounts as applicable', 'About 15 minutes for individuals; about 1 day 11 minutes for corporations', 52, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(54, 'Municipal Treasurer\'s Office', 'Community Tax Certificate (Cedula)', 'cedula\ncommunity tax certificate\nctc', 'Accomplished information slip\nValid government-issued ID\nProof of income/current payslip\nFor corporations: Business Permit and Income Tax Return', '₱5 basic + ₱1 per ₱1,000 of income for individuals (max ₱5,000); ₱500 basic + ₱2 per ₱5,000 of sales/property for corporations (max ₱10,000)', 'About 3 minutes', 53, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(55, 'Municipal Treasurer\'s Office', 'Tax Clearance', 'tax clearance', 'Previous RPT Official Receipt, latest Tax Declaration, or Title\nValid government-issued ID\nLatest Community Tax Certificate (Cedula)', '₱100', 'About 15 minutes', 54, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(56, 'Norzagaray Municipal Hospital', 'Out-Patient Treatment', 'hospital outpatient\nhospital consultation\nopd norzagaray', 'Just your personal appearance at the Outpatient Department', 'Free, or based on the billing statement if medicines/lab tests are needed', 'About 20–40 minutes', 55, '2026-08-15 00:33:54', '2026-08-15 00:33:54'),
(57, 'Norzagaray Municipal Hospital', 'Emergency Services', 'hospital emergency\nemergency room norzagaray', 'Your personal appearance\nOne companion/relative', 'Free, or based on the billing statement', 'Case-to-case basis', 56, '2026-08-15 00:33:54', '2026-08-15 00:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `news_posts`
--

CREATE TABLE `news_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `category` varchar(60) NOT NULL DEFAULT 'Official Announcement',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news_posts`
--

INSERT INTO `news_posts` (`id`, `category`, `title`, `description`, `image`, `is_featured`, `published_at`, `created_at`, `updated_at`) VALUES
(2, 'Official Announcement', 'Norzagaray Casayahan Music Festival', 'An unforgettable night of live music in Norzagaray, Bulacan, featuring the iconic Filipino band Orange & Lemons!. Experience their timeless hits, sing along to your favorite songs, and enjoy an electrifying evening filled with great music, good vibes, and unforgettable memories. Don’t miss the chance to see Orange & Lemons live!', 'assets/uploads/news/db5069a940d84068d39e2c80.jpg', 0, '2026-08-16', '2026-08-16 09:28:32', '2026-08-16 09:28:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'System Admin', 'admin@norzagaray.gov.ph', '$2y$10$O4OhYIqM1atikiHf4J4yxeeJg0O4DnyrzGcKATe3tA8b1qHqN7K/6', 'admin', '2026-08-14 12:28:34'),
(2, 'Juan Dela Cruz', 'juan.delacruz@example.com', '$2y$10$Ok1.2eMQqL8t5VcCSHq1t.YaW6oy9TOtD7WV2vgBmPOwW.O4zz3JO', 'user', '2026-08-14 12:28:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `citizens_charter_services`
--
ALTER TABLE `citizens_charter_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_posts`
--
ALTER TABLE `news_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `citizens_charter_services`
--
ALTER TABLE `citizens_charter_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `news_posts`
--
ALTER TABLE `news_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
