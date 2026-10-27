<?php
/**
 * One-time update: adds the "how to apply" steps and "who may avail" to the Citizen's Charter, taken
 * from the Norzagaray Citizen's Charter 2025 (the Client Steps / Agency Action tables). Run once via:
 *   php sql/migrate-charter-steps.php
 *
 * - citizens_charter_services.steps (one step per line) and .who_may_avail.
 * - Fills them in for the 57 services. A service is matched by its id AND its original title; one whose
 *   title was changed since is skipped and listed, and so is one that already has steps (nothing an
 *   administrator wrote is overwritten). Add those by hand in the Administrator's Citizen's Charter page.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

if (!$pdo->query("SHOW COLUMNS FROM citizens_charter_services LIKE 'steps'")->fetch()) {
    $pdo->exec('ALTER TABLE citizens_charter_services ADD COLUMN steps TEXT NULL AFTER requirements');
    echo "citizens_charter_services.steps added
";
}
if (!$pdo->query("SHOW COLUMNS FROM citizens_charter_services LIKE 'who_may_avail'")->fetch()) {
    $pdo->exec('ALTER TABLE citizens_charter_services ADD COLUMN who_may_avail VARCHAR(500) NULL AFTER title');
    echo "citizens_charter_services.who_may_avail added
";
}

$rows = json_decode(<<<'JSON'
[
 {
  "id": 1,
  "title": "Issuance of Business Permit (New)",
  "who": "Businessmen that own a business establishment within the municipality",
  "steps": [
   "Fill out the application form and attach the documents required from the concerned offices: MENRO (Certificate of Non-Coverage/ECC, ₱1,000 for the CNC), MPDO (Zoning/Locational Clearance, fee based on MPDO assessment), the Engineering Office (Occupancy Permit), the Rural Health Office (Sanitary Permit) and BFP (Fire Safety Inspection Certificate, fee based on BFP assessment). About 15 minutes.",
   "Submit the signed application form. The office verifies the documents and assesses the fees and charges for payment (about 10 minutes), then prepares your Tax Order of Payment (about 5 minutes).",
   "Pay the assessed fees and charges and receive your Official Receipt (about 5 minutes).",
   "Wait while your Business Permit is encoded and printed (about 10 minutes), then recommended and approved by the Municipal Mayor (about 1 day).",
   "Claim your Business Permit."
  ]
 },
 {
  "id": 2,
  "title": "Renewal of Business Permit",
  "who": "Businessmen that own a business establishment within the municipality",
  "steps": [
   "Fill out the application form and attach the documents required from the concerned offices: MENRO (Certificate of Non-Coverage/ECC, ₱1,000 for the CNC), MPDO (Zoning/Locational Clearance, fee based on MPDO assessment), the Engineering Office (Occupancy Permit), the Rural Health Office (Sanitary Permit) and BFP (Fire Safety Inspection Certificate, fee based on BFP assessment). About 15 minutes.",
   "Submit the signed application form. The office verifies the documents and assesses the fees and charges for payment (about 10 minutes), then prepares your Tax Order of Payment (about 5 minutes).",
   "Pay the assessed fees and charges and receive your Official Receipt (about 5 minutes).",
   "Wait while your Business Permit is encoded and printed (about 10 minutes), then recommended and approved by the Municipal Mayor (about 1 day).",
   "Claim your Business Permit."
  ]
 },
 {
  "id": 3,
  "title": "Motorized Tricycle Operator's Permit (MTOP)",
  "who": "Tricycle operators within the municipality",
  "steps": [
   "Submit the documents required for your MTOP application. The office verifies the submitted documents (about 15 minutes) and inspects the tricycle unit.",
   "Pay the assessed fees and charges (₱575 for new, ₱210 for renewal) and receive your Official Receipt (about 5 minutes).",
   "Wait while your MTOP is encoded and printed (about 10 minutes), then recommended and approved by the Municipal Mayor (about 1 day).",
   "Claim your MTOP."
  ]
 },
 {
  "id": 4,
  "title": "Transfer of Tax Declaration to New Owner/s",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit the complete documents. A staff member interviews you and verifies the documents you presented (about 15 minutes).",
   "Pay the corresponding fee, ₱200 per Tax Declaration, and receive your Official Receipt (about 5 minutes).",
   "Present your proof of payment (OR). The office receives your receipt (about 5 minutes), encodes the approved documents in the Real Property Tax Information System and prints the new Tax Declaration, Field Appraisal and Assessment Sheet and Notice of Assessment (about 30 minutes per Tax Declaration), then the new Tax Declaration is reviewed and signed (about 3 minutes per Tax Declaration).",
   "Claim your Tax Declaration. The original copy is released to you (about 3 minutes)."
  ]
 },
 {
  "id": 5,
  "title": "Consolidation and Subdivision of Property",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit the complete documents. A staff member interviews you and verifies the documents you presented (about 15 minutes).",
   "Pay the corresponding fee, ₱200 per Tax Declaration, and receive your Official Receipt (about 5 minutes).",
   "Present your proof of payment (OR). The office receives your receipt (about 5 minutes), encodes the approved documents in the Real Property Tax Information System and prints the new Tax Declaration, Field Appraisal and Assessment Sheet and Notice of Assessment (about 30 minutes per Tax Declaration), then the new Tax Declaration is reviewed and signed (about 3 minutes per Tax Declaration).",
   "Claim your Tax Declaration. The original copy is released to you (about 3 minutes)."
  ]
 },
 {
  "id": 6,
  "title": "Demolition and Cancellation of Property Record",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit the complete requirements. The office verifies the documents presented (about 10 minutes) and conducts an ocular inspection of the property (2 to 8 hours, depending on the location of the property).",
   "Present your proof of payment (OR). The office cancels the property in the database and prints the Notice of Cancellation (about 15 minutes), then the Notice of Cancellation is reviewed and signed (about 3 minutes).",
   "Claim your Notice of Cancellation (about 3 minutes)."
  ]
 },
 {
  "id": 7,
  "title": "Reassessment / Assessment of Property",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit the complete requirements. The office verifies the documents presented (about 10 minutes) and conducts an ocular inspection of the property (2 to 8 hours, depending on the location of the property).",
   "Pay the corresponding fee, ₱200 per Tax Declaration, and receive your Official Receipt (about 5 minutes).",
   "Present your proof of payment (OR). The office receives your receipt (about 5 minutes), encodes the approved documents in the Real Property Tax Information System and prints the new Tax Declaration, Field Appraisal and Assessment Sheet and Notice of Assessment (about 30 minutes per Tax Declaration), then the new Tax Declaration is reviewed and signed (about 3 minutes per Tax Declaration).",
   "Claim your Tax Declaration. The original copy is released to you (about 3 minutes)."
  ]
 },
 {
  "id": 8,
  "title": "Reclassification of Property",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit the complete documents. The office verifies the documents presented (about 10 minutes).",
   "Pay the corresponding fee, ₱200 per Tax Declaration, and receive your Official Receipt (about 5 minutes).",
   "Present your proof of payment (OR). The office receives your receipt, encodes the approved documents in the Real Property Tax Information System and prints the new Tax Declaration, Field Appraisal and Assessment Sheet and Notice of Assessment (about 30 minutes per Tax Declaration), then the new Tax Declaration is reviewed and signed (about 3 minutes per Tax Declaration).",
   "Claim your Tax Declaration. The original copy is released to you (about 3 minutes)."
  ]
 },
 {
  "id": 9,
  "title": "Tax Declaration for Newly Assessed Land, Building, or Machinery",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit the complete requirements. The office verifies the documents presented (about 10 minutes) and conducts an ocular inspection of the property (2 to 8 hours, depending on the location of the property).",
   "Pay the corresponding fee, ₱200 per Tax Declaration, and receive your Official Receipt (about 5 minutes).",
   "Present your proof of payment (OR). The office receives your receipt (about 5 minutes), encodes the approved documents in the Real Property Tax Information System and prints the new Tax Declaration, Field Appraisal and Assessment Sheet and Notice of Assessment (about 30 minutes per Tax Declaration), then the new Tax Declaration is reviewed and signed (about 3 minutes per Tax Declaration).",
   "Claim your Tax Declaration. The original copy is released to you (about 3 minutes)."
  ]
 },
 {
  "id": 10,
  "title": "Certified True Copy of Tax Declaration",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit your requirements. The office verifies the documents presented.",
   "Pay the corresponding fee, ₱100 per Tax Declaration, and receive your Official Receipt.",
   "Present your proof of payment (OR). The office checks and prints the Certified True Copy of the Tax Declaration, then it is reviewed and signed.",
   "Claim your Certified True Copy of the Tax Declaration."
  ]
 },
 {
  "id": 11,
  "title": "Property Certifications (With/Without Improvement, Land Holdings, No Property)",
  "who": "Property owner or authorized representative",
  "steps": [
   "Submit your requirements. The office verifies the documents presented.",
   "Pay the corresponding fee, ₱100 per certification, and receive your Official Receipt.",
   "Present your proof of payment (OR). The office checks and prints the certification, then it is reviewed and signed.",
   "Claim your certificate or certificates."
  ]
 },
 {
  "id": 12,
  "title": "Certified True Copy of Birth / Death / Marriage Certificate",
  "who": "",
  "steps": [
   "Fill up the verification slip. The office asks for a valid ID and verifies your record in the Civil Registration Information System (CRIS), ₱100 (about 5 minutes).",
   "Go to the Municipal Treasury Office to pay the fees (₱100). The office then prints the Certification/Certified True Copy (about 5 minutes).",
   "Return to the Municipal Civil Registrar's Office and present your Official Receipt. The Certification/Certified True Copy is signed (about 1 minute).",
   "Claim your document (about 1 minute)."
  ]
 },
 {
  "id": 13,
  "title": "Registration of Birth, Death, or Marriage",
  "who": "",
  "steps": [
   "Submit the Birth, Death or Marriage Certificate. The office checks it and codes it (about 5 minutes each).",
   "For registration of a Death, go to the Municipal Treasury Office to pay the fees, then return to the Municipal Civil Registrar's Office. Fees under the Local Revenue Code: Birth ₱100, Marriage ₱100; Death: Burial ₱50, Miscellaneous ₱100 and Transfer of Cadaver ₱200, as applicable.",
   "Present your Official Receipt (for Birth and Marriage) and wait for the final approval of the registration. The office assigns the registry number and records it in the Registry Book (about 5 minutes each), the Civil Registrar approves and signs the duly accomplished form (about 3 minutes), and the document is encoded in CRIS (about 5 minutes) and segregated into the office file and the OCRG copy (about 5 minutes).",
   "Claim the registered civil registry document (about 3 minutes)."
  ]
 },
 {
  "id": 14,
  "title": "Application for Marriage License",
  "who": "",
  "steps": [
   "Fill up the information sheet and submit the required documents. The office interviews you and checks and verifies the requirements (about 10 minutes).",
   "The office issues your order of payment slip (about 5 minutes). Go to the Municipal Treasury Office to pay the fees (Application for Marriage License ₱398 and Marriage License ₱2), then return to the Municipal Civil Registrar's Office and present your Official Receipt.",
   "The office prepares your application for marriage license (about 7 minutes) and types the sworn statement/advice/consent (about 5 minutes).",
   "Review and sign the application for marriage license, and sign it in the presence of the administering officer. The documents are countersigned, reviewed and signed, a claim stub is issued to you, and the notice is posted and recorded in the Registry Book.",
   "After 10 calendar days, claim your marriage license."
  ]
 },
 {
  "id": 15,
  "title": "Late Registration / Out-of-Town Registration of Birth, Death, or Marriage",
  "who": "",
  "steps": [
   "Submit all the required documents. The office receives and reviews them (about 10 minutes).",
   "Pay ₱150 (Late Registration, Local Revenue Code) to the cashier and receive your Official Receipt (about 3 minutes).",
   "Present your proof of payment. The office prepares all the documents and tells you the date of release (about 5 minutes). Documents for late registration are released after 10 days of posting.",
   "Claim your registered Birth, Death or Marriage Certificate. It is signed and released to you (about 3 minutes)."
  ]
 },
 {
  "id": 16,
  "title": "Supplemental Report (correcting an omitted entry)",
  "who": "",
  "steps": [
   "Submit the complete requirements. The office receives and reviews them (about 10 minutes).",
   "Pay ₱150 (Local Revenue Code) to the cashier and receive your Official Receipt (about 3 minutes).",
   "Present your proof of payment. The office processes and prepares the documents for the supplemental report (about 15 minutes), then transmits them to the PSA Regional Office for annotation (about 1 day). Payment of the transmission is shouldered by the client depending on the LBC rate.",
   "Wait for the annotated Birth Certificate with the Supplemental Report. PSA releases it to you after about 1 month."
  ]
 },
 {
  "id": 17,
  "title": "RA 9255 — Affidavit to Use the Surname of the Father (AUSF)",
  "who": "",
  "steps": [
   "Submit the complete requirements and documents. The office receives and reviews them (about 10 minutes).",
   "Pay ₱150 (Local Revenue Code) to the cashier and receive your Official Receipt (about 3 minutes).",
   "Present your proof of payment. The office processes and prepares the documents for the AUSF (about 15 minutes) and transmits them to the PSA Regional Office for annotation (about 1 day). Payment of the transmission is paid by the client depending on the LBC rate.",
   "Wait for the annotated Birth Certificate. PSA releases it to you after about 1 month."
  ]
 },
 {
  "id": 18,
  "title": "Legitimation",
  "who": "",
  "steps": [
   "Submit the complete requirements and documents. The office receives and reviews them (about 10 minutes).",
   "Pay ₱150 (Local Revenue Code) to the cashier and receive your Official Receipt (about 5 minutes).",
   "Present your proof of payment. The office processes and prepares the documents for legitimation (about 15 minutes) and transmits them to the PSA Central Office for annotation (about 1 day). Payment of the transmission is paid by the client depending on the LBC rate.",
   "Wait for the annotated Birth Certificate. PSA releases it to you after about 1 month."
  ]
 },
 {
  "id": 19,
  "title": "Petition for Correction — RA 10172 (Gender / Date / Month of Birth)",
  "who": "",
  "steps": [
   "Submit the complete requirements and documents. The office receives and reviews them.",
   "Pay ₱3,000 (Local Revenue Code) to the cashier and receive your Official Receipt.",
   "Present your proof of payment. The office prepares the Notice of Posting, Certification of Posting and the other documents needed for the correction of gender, date and month, then posts the petition on the LGU bulletin board and in a newspaper of general circulation (the publication fee of ₱1,500 is paid by you to the publisher).",
   "On the fifteenth day, the office transmits the petition. The affirmed petition is obtained from the PSA Central Office after about 90 days, which can take 2 to 3 months depending on the decision of the PSA Legal Office.",
   "After PSA affirms the petition, get your copy of the finality from the Municipal Civil Registry Office. The office processes the finality documents, has them reviewed and signed, and transmits the petition to the PSA Region for annotation. Payment of the transmission of the petition is paid by the client depending on the LBC rate.",
   "Wait for the annotated Birth Certificate. PSA releases it to you about 30 days later."
  ]
 },
 {
  "id": 20,
  "title": "RA 9048 — Correction of Clerical Error",
  "who": "",
  "steps": [
   "Submit the complete requirements and documents for the correction. The office receives and reviews them.",
   "Pay ₱1,000 (Local Revenue Code) to the cashier and receive your Official Receipt.",
   "Present your proof of payment. The office prepares the documents needed for the correction of the clerical error and posts the petition on the LGU bulletin board (10 days).",
   "On the fifteenth day, the office transmits the petition. The affirmed petition is obtained from the PSA Central Office after about 60 days, which can take 2 to 3 months depending on the decision of the PSA Legal Officer.",
   "After PSA affirms the petition, get your copy of the finality from the Municipal Civil Registry Office. The office processes the finality documents, has them reviewed and signed, and transmits the petition to the PSA Region for annotation. Payment of the transmission of the petition is paid by the client depending on the LBC rate.",
   "Wait for the annotated Birth, Death or Marriage Certificate. PSA releases it to you about 30 days later."
  ]
 },
 {
  "id": 21,
  "title": "RA 9048 — Petition for Change of First Name",
  "who": "",
  "steps": [
   "Submit the complete requirements and documents for the correction. The office receives and reviews them.",
   "Pay ₱3,000 (Local Revenue Code) to the cashier and receive your Official Receipt.",
   "Present your proof of payment. The office prepares the documents needed for the change of first name and posts the petition on the LGU bulletin board and in a newspaper of general circulation (the publication fee of ₱1,500 is paid by you to the publisher).",
   "On the fifteenth day, the office transmits the petition. The affirmed petition is obtained from the PSA Central Office after about 60 days, which can take 2 to 3 months depending on the decision of the PSA Legal Officer.",
   "After PSA affirms the petition, get your copy of the finality from the Municipal Civil Registry Office. The office processes the finality documents, has them reviewed and signed, and transmits the petition to the PSA Region for annotation. Payment of the transmission of the petition is paid by the client depending on the LBC rate.",
   "Wait for the annotated Birth, Death or Marriage Certificate. PSA releases it to you about 30 days later."
  ]
 },
 {
  "id": 22,
  "title": "Endorsement of Negative Certification / Clear Copy / Advance Copy",
  "who": "",
  "steps": [
   "Submit your accomplished request slip. The office verifies the information in the database and prints the requested certification.",
   "Pay ₱150 (Local Revenue Code) to the cashier and receive your Official Receipt.",
   "Present your proof of payment. The office reviews, signs and releases the requested certification, then transmits the petition to the PSA Region for annotation through LBC. Payment of the transmission of the petition is paid by the client depending on the LBC rate.",
   "Wait for the annotated Birth Certificate. PSA releases it to you about one month later."
  ]
 },
 {
  "id": 23,
  "title": "Building Permit",
  "who": "Building applicants",
  "steps": [
   "Submit your online application with the complete requirements. Register and file your application at https://bldg.ibpls.com/norzagaraybulacan/. The office reviews the filed application to check that the requirements are complete (about 1 day).",
   "Your building plans are evaluated at the same time. The plans are checked and approved (about 30 minutes).",
   "The fees and charges are assessed automatically and an Order of Payment is generated (about 30 minutes), based on the fee schedule of the National Building Code of the Philippines and local ordinances and resolutions.",
   "Pay the necessary fees. The office receives your payment and issues an Official Receipt (about 5 minutes).",
   "Declare and photocopy your Official Receipt. The office receives and verifies it.",
   "Acquire your Building Permit. It is prepared (about 2 minutes) and released to you (about 15 minutes)."
  ]
 },
 {
  "id": 24,
  "title": "Certificate of Occupancy",
  "who": "Certificate of Occupancy applicants",
  "steps": [
   "Submit your online application with the complete requirements at https://co.ibpls.com/norzagaraybulacan/. The office reviews the filed application to check that the requirements are complete (about 20 minutes).",
   "Your plans are verified and the building is inspected at the same time (about 1 day).",
   "The fees and charges are assessed and an Order of Payment is generated, based on the fee schedule of the National Building Code of the Philippines and local ordinances and resolutions.",
   "Pay the necessary fees. The office receives your payment and issues an Official Receipt (about 5 minutes).",
   "Declare and photocopy your Official Receipt. The office verifies it and processes the permit (about 15 minutes).",
   "Acquire your Certificate of Occupancy. It is released to you (about 3 minutes)."
  ]
 },
 {
  "id": 25,
  "title": "Certificate of Electrical Inspection and Wiring Permit",
  "who": "Applicants for a Certificate of Electrical Inspection and Wiring Permit",
  "steps": [
   "Fill up the application form. The office checks it and sketches the place to be inspected (about 5 minutes).",
   "Joint inspection with your electric service provider. The office conducts the ocular inspection (about 2 hours).",
   "Submit the complete requirements. The office receives and reviews them (about 10 minutes).",
   "Pay the necessary fees, based on the fee schedule of the National Building Code. The office receives your payment and issues an Official Receipt (about 5 minutes).",
   "Declare and photocopy your Official Receipt. The office receives it and processes the permit (about 5 minutes).",
   "Acquire your permit. It is released to you (about 5 minutes)."
  ]
 },
 {
  "id": 26,
  "title": "Medical Consultation",
  "who": "All citizens of Norzagaray",
  "steps": [
   "New patients accomplish the consent form for the Electronic Medical Record (EMR). The office registers you and gives you a queue number (about 15 minutes). Patients with an existing record just get a queue number.",
   "Simple cases without diagnostics: give all the information needed. Your vital signs are taken and recorded (about 5 minutes), then you are examined, diagnosed and treated (about 10 minutes).",
   "Cases needing referral and diagnostics: give the information needed by the doctor. The doctor takes your history and examines you (about 15 minutes), and requests routine laboratory examinations and analyzes the results (about 15 minutes). If a special exam is needed, such as a sputum test, submit the specimen needed (about 20 minutes, with results within about 24 hours). The doctor then makes a diagnosis and treats you (about 10 minutes), and gives home instructions or refers you if the case cannot be managed here.",
   "Proceed to the pharmacy for dispensing of medicines (about 1 minute). Available medicine is provided and home instructions are given."
  ]
 },
 {
  "id": 27,
  "title": "Dental Consultation",
  "who": "All citizens of Norzagaray",
  "steps": [
   "New patients accomplish the consent form for the Electronic Medical Record (EMR). The office registers you and gives you a queue number (about 15 minutes). Patients with an existing record just get a queue number.",
   "Queue for dental consultation or procedure. The staff fill up your dental record and take your vital signs.",
   "The dentist does a dental assessment and management or tooth extraction, an oral examination for pregnant women, or oral prophylaxis, as needed.",
   "The dental record is updated, and you are given home instructions and discharged."
  ]
 },
 {
  "id": 28,
  "title": "Maternal Health Care Services",
  "who": "All citizens of Norzagaray",
  "steps": [
   "New clients accomplish the Individual Treatment Record and get a queue number. The office registers you (about 5 minutes). Clients with an existing record present their maternal and child booklet and get a queue number.",
   "Queue for maternal health care services. The staff fill up your record, take your vital signs and issue the maternal and child booklet (about 5 minutes).",
   "The health worker assesses you and makes a management plan, gives health education on proper nutrition and maternal care, administers immunization if needed, reminds you of your follow-up schedule and refers you as needed (about 10 minutes).",
   "Laboratory services, as needed. A specimen is collected and processed (about 15 minutes)."
  ]
 },
 {
  "id": 29,
  "title": "National Immunization Program",
  "who": "All citizens of Norzagaray",
  "steps": [
   "New clients accomplish the Individual Treatment Record and get a queue number. The office registers you (about 5 minutes). Clients with an existing record present their maternal and child booklet and get a queue number.",
   "Queue for immunization services. The staff fill up your record, take your vital signs and issue your NIP card (about 5 minutes).",
   "The health worker assesses you, provides the National Immunization Program services and conducts health education on the NIP (about 10 minutes)."
  ]
 },
 {
  "id": 30,
  "title": "Family Planning Services",
  "who": "All citizens of Norzagaray",
  "steps": [
   "New clients accomplish Family Planning Form 1 and get a queue number. The office registers you (about 5 minutes). Clients with an existing record secure their family planning record and get a queue number.",
   "Queue for family planning consultation or services. The staff fill up your record and take your vital signs (about 5 minutes).",
   "The health worker assesses you and provides the family planning services, conducts health education on family planning and related topics, and reminds you of your follow-up schedule (about 10 minutes)."
  ]
 },
 {
  "id": 31,
  "title": "Processing/Issuance of Death Certificate & Exhumation Permit",
  "who": "All citizens of Norzagaray",
  "steps": [
   "Present your documents (a filled-out Death Certificate, the official medical records or medical certificate, and, for exhumation, the original copy of the death certificate). The Municipal Health Officer reviews them (about 5 minutes).",
   "Pay ₱200 for the certificate or permit, as listed in the Citizen's Charter (LCR, MTO).",
   "The Death Certificate and the Exhumation Permit are issued (about 5 to 10 minutes)."
  ]
 },
 {
  "id": 32,
  "title": "Health Card for Employment",
  "who": "Clients",
  "steps": [
   "Submit your medical laboratory results. The office evaluates the documents presented (about 5 minutes).",
   "If your results passed, pay ₱100 and your Health Card is issued (about 5 minutes).",
   "If your results did not pass, you are referred to a doctor for proper treatment and medication (about 5 minutes)."
  ]
 },
 {
  "id": 33,
  "title": "Sanitary Permit",
  "who": "Clients",
  "steps": [
   "Provide information on your business and the other necessary documents (ECC, discharge permit, permit to operate, application permit, medical results of employees if necessary, and water test results for water refilling stations).",
   "If the business is not complex, pay the fee (₱300 to ₱2,550) and the Sanitary Permit is issued (about 5 minutes).",
   "If the business is complex, your business establishment is inspected (about 1 day), an inspection report is issued, and it is endorsed to the Provincial Health Office and other necessary agencies."
  ]
 },
 {
  "id": 34,
  "title": "Potability Certificate",
  "who": "Clients",
  "steps": [
   "Provide information on your business and the other necessary documents, such as the water test results for water refilling stations from a DOH-accredited laboratory. The office evaluates the documents presented (about 5 minutes).",
   "Pay ₱100 and your Potability Certificate is issued."
  ]
 },
 {
  "id": 35,
  "title": "Zoning Certification for Business Permit — Non-Critical Projects (New)",
  "who": "Business owners",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks that the requirements are complete (about 5 minutes), then verifies the zoning classification based on the approved Zoning Ordinance. If the business conforms to the Zoning Ordinance and the requirements are complete, an Order of Payment is issued. The Zoning Certification is then printed, reviewed and signed.",
   "Pay the verification fee (₱500 per hectare) at the Municipal Treasurer's Office and submit a photocopy of the Official Receipt. The office checks the Official Receipt.",
   "Receive your Zoning Certification. It is recorded in the log book and released to you (about 3 minutes)."
  ]
 },
 {
  "id": 36,
  "title": "Zoning Certification for Business Permit — Critical Projects (New)",
  "who": "Business owners",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks that the requirements are complete, then verifies the zoning classification based on the approved Zoning Ordinance. If the project conforms to the Zoning Ordinance and the requirements are complete, an Order of Payment is issued. For critical projects, a site inspection is required (about 120 minutes, or within 48 hours, subject to the availability of the zoning inspector). If the application does not conform to the Zoning Ordinance, it is denied with a statement of the deficiencies and recommended corrective measures. The Zoning Certification is then printed, reviewed and signed.",
   "Pay the verification fee at the Municipal Treasurer's Office and submit a photocopy of the Official Receipt. The office checks the Official Receipt.",
   "Receive your Zoning Certification. It is recorded in the log book and released to you (about 3 minutes)."
  ]
 },
 {
  "id": 37,
  "title": "Zoning Certification for Renewal of Business Permit",
  "who": "Business owners",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks that the requirements are complete (about 5 minutes), then validates whether there are amendments or violated conditions in your original Zoning Certification. If you are compliant and the requirements are complete, the evaluator initials the back of the application form. If you are not compliant, the office explains the corrective measures to you.",
   "Receive the application form with the evaluator's signature. It is recorded in the logbook and released to you (about 5 minutes)."
  ]
 },
 {
  "id": 38,
  "title": "Zoning Certification (as to Land Use)",
  "who": "Real property owners, developers and students",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks that the requirements are complete (about 5 minutes), then verifies the zoning classification based on the approved Zoning Ordinance. If the business conforms to the Zoning Ordinance and the requirements are complete, an Order of Payment is issued. The Zoning Certification is then printed, reviewed and signed.",
   "Pay the verification fee (₱500 per hectare) at the Municipal Treasurer's Office and submit a photocopy of the Official Receipt. The office checks the Official Receipt.",
   "Receive your Zoning Certification. It is recorded in the log book and released to you (about 3 minutes)."
  ]
 },
 {
  "id": 39,
  "title": "Locational Clearance for Building Permit — Critical Projects",
  "who": "Real property owners and developers",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks that the requirements are complete, then verifies the zoning classification based on the approved Zoning Ordinance. If the project conforms to the Zoning Ordinance and the requirements are complete, an Order of Payment is issued. For critical projects, a site inspection is required (about 120 minutes, or within 48 hours, subject to the availability of the zoning inspector). If the application does not conform to the Zoning Ordinance, it is denied with a statement of the deficiencies and recommended corrective measures. The Zoning Certification is then printed, reviewed and signed.",
   "Pay the verification fee at the Municipal Treasurer's Office and submit a photocopy of the Official Receipt. The office checks the Official Receipt.",
   "Receive your Zoning Certification. It is recorded in the log book and released to you (about 3 minutes)."
  ]
 },
 {
  "id": 40,
  "title": "Environmental Clearance for Business Permit — Non-Critical (New)",
  "who": "Filipino citizens at least 18 years old who are the registered or legitimate owner of the business, or a designated representative with a valid authorization from the owner",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks and assesses that the requirements are complete (about 4 minutes).",
   "The office verifies the authenticity of your Certificate of Non-Coverage through the EMB online link, records the business or project, and signs the application form.",
   "Receive your signed application form. Environmental inspection, solid waste management and other relevant fees are included in the assessment of the project."
  ]
 },
 {
  "id": 41,
  "title": "Environmental Clearance for Business Permit — Non-Critical (Renewal)",
  "who": "Filipino citizens at least 18 years old who are the registered or legitimate owner of the business, or a designated representative with a valid authorization from the owner",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks and assesses that the requirements are complete (about 4 minutes).",
   "Go to the Municipal Treasurer's Office, pay ₱1,000 for the Local Certificate of Non-Coverage, and present the original Official Receipt. The office checks it (about 1 minute).",
   "The office issues the original copy of the Local Certificate of Non-Coverage and records the business or project (about 4 minutes), then signs your application form (about 1 minute). Environmental inspection, solid waste management and other relevant fees are included in the assessment of the project."
  ]
 },
 {
  "id": 42,
  "title": "Environmental Clearance for Business Permit — Critical Projects/Areas",
  "who": "Filipino citizens at least 18 years old who are the registered or legitimate owner of the business, or a designated representative with a valid authorization from the owner",
  "steps": [
   "Fill out and submit the application form with the complete requirements. The office receives it and checks and assesses that the requirements are complete.",
   "Present your Environmental Compliance Certificate (ECC) and Provincial Government Acknowledgement (PGA) Certificate. For a renewal, also submit the other permits required by the conditions in the ECC: the Pollution Control Officer (PCO) Accreditation Certificate, Permit to Operate, Wastewater Discharge Permit and Hazardous Waste Generation ID, if applicable. The office verifies the authenticity of the ECC online and, for a renewal, assesses whether you comply with the conditions in the ECC.",
   "The office records the business or project and signs your application form (about 20 minutes in all). Environmental inspection, solid waste management, environmental polluters hazard and other relevant fees are included in the assessment of the project."
  ]
 },
 {
  "id": 43,
  "title": "Certificate of No Objection for Tree Cutting Permit",
  "who": "Filipino citizens at least 18 years old who are the registered or legitimate owner of the land, or a designated representative with a valid authorization or Special Power of Attorney from the owner",
  "steps": [
   "Submit your application with the complete requirements. The office receives it and checks and assesses that the requirements are complete (about 5 minutes).",
   "The office informs you of the schedule of the joint ocular validation and inspection with CENRO-DENR Tabang. Assist the inspection team, made up of MENRO and CENRO-DENR personnel, as the trees applied for cutting are inspected (about 1 day).",
   "The office prepares the Certificate of No Objection (about 1 day, depending on the availability of the signatories at the Office of the Mayor).",
   "The office informs you that the Certificate of No Objection is ready and issues an Order of Payment (about 5 minutes).",
   "Go to the Municipal Treasurer's Office and pay the tree cutting fee (₱300 per tree), the certification fee (₱100) and other relevant fees, then present the Official Receipt (about 5 minutes).",
   "Receive your Certificate of No Objection with the official dry seal (about 5 minutes). The environmental inspection fee is ₱1,000."
  ]
 },
 {
  "id": 44,
  "title": "Assistance to Individuals in Crisis Situation (AICS)",
  "who": "Patients or their immediate family members",
  "steps": [
   "Submit the complete requirements. The MSWD staff evaluate the documents you submitted (about 2 minutes).",
   "Provide your socio-economic information. The staff interview you and assess your situation (about 10 minutes).",
   "Wait for the approval of your application (about 1 minute). The voucher is then filed (about 1 minute).",
   "Receive your payment stub. The release of the financial assistance itself is handled by the Municipal Treasurer's Office."
  ]
 },
 {
  "id": 45,
  "title": "MSWDO Certification (e.g. Guardianship)",
  "who": "Clients",
  "steps": [
   "Submit the complete requirements. The MSWD staff check that the documents are complete (about 2 minutes).",
   "Provide the information needed to complete the assessment. A social worker interviews and assesses you (about 10 minutes).",
   "The certification is prepared and signed, then released to you."
  ]
 },
 {
  "id": 46,
  "title": "Social Case Study Report (SCSR)",
  "who": "Clients",
  "steps": [
   "Submit your reference documents (such as a medical protocol, legal protocol or other case reference). The office pre-screens the documents (about 5 minutes).",
   "Provide your personal and socio-economic data. A social worker conducts the initial interview and assessment (about 30 minutes) and a home visit (about 2 to 4 hours).",
   "The Social Case Study Report is prepared and encoded: a simple SCSR takes about 30 minutes and a comprehensive SCSR about 3 hours.",
   "The SCSR is submitted to the referring agency or court.",
   "For medical, educational or burial assistance, the simpler version of the report is prepared in about 33 minutes. Submit your requirements, the staff check the documents, interview and assess you, then prepare and release the report."
  ]
 },
 {
  "id": 47,
  "title": "Adoption",
  "who": "Prospective adoptive parents",
  "steps": [
   "Submit the complete documents. The MSWDO reviews the completeness of the documents (about 10 minutes).",
   "A social worker interviews and assesses you (about 30 minutes).",
   "A home visit is conducted (about 7 working days).",
   "The Home Study Report is prepared and released (about 30 working days)."
  ]
 },
 {
  "id": 48,
  "title": "Person With Disability (PWD) ID",
  "who": "Residents of Norzagaray who have a disability",
  "steps": [
   "Submit the required documents. The staff check that the documents are complete (about 2 minutes).",
   "The staff interview and assess you (about 3 minutes).",
   "Your PWD ID is prepared and released to you (about 2 minutes)."
  ]
 },
 {
  "id": 49,
  "title": "Solo Parent's ID",
  "who": "Solo parents who are residents of Norzagaray",
  "steps": [
   "Submit the required documents. The staff check that the documents are complete (about 3 minutes).",
   "The staff interview and assess you (about 5 minutes).",
   "A home visit is conducted (about 7 working days).",
   "Your Solo Parent's ID is prepared and released to you (about 3 minutes)."
  ]
 },
 {
  "id": 50,
  "title": "Senior Citizen's ID",
  "who": "Senior citizens who are residents of Norzagaray",
  "steps": [
   "Fill up the form and submit the required documents. The staff check that the documents are complete (about 2 minutes).",
   "Your Senior Citizen's ID is prepared and released to you (about 5 minutes)."
  ]
 },
 {
  "id": 51,
  "title": "Livelihood Assistance",
  "who": "Clients",
  "steps": [
   "Fill up the application form and submit the required documents. The staff check that the documents are complete (about 5 minutes).",
   "The staff conduct an initial interview and assessment (about 15 minutes).",
   "A home visit is conducted (about 7 working days).",
   "The application is submitted to the MSWDO for approval (about 5 minutes) and endorsed to the Budget Office for processing (about 3 minutes).",
   "The assistance is released to you by the Municipal Treasurer's Office."
  ]
 },
 {
  "id": 52,
  "title": "Provision of Assistive Devices",
  "who": "Clients",
  "steps": [
   "Submit the complete requirements. The staff check that the documents are complete (about 3 minutes).",
   "The staff interview and assess you (about 10 minutes).",
   "The assistive device is released to you."
  ]
 },
 {
  "id": 53,
  "title": "Real Property Tax (RPT) Receipt / Payment",
  "who": "Any individual or business (natural or juridical person) that owns property within Norzagaray, Bulacan",
  "steps": [
   "Submit the complete requirements. The office receives them and reviews the real property tax (about 5 minutes).",
   "Wait for the RPT assessment. For individuals, the tax due is assessed (about 4 minutes). For corporations, wait for the advice that the tax bill is already available (about 1 day).",
   "Pay the tax due: 1% of the assessed value (Basic Real Property Tax) plus 1% of the assessed value (Special Education Fund), plus penalties or less discounts, whichever applies. The office receives your payment and issues your Official Receipt (about 5 minutes).",
   "Claim your Real Property Tax Official Receipt (about 1 minute)."
  ]
 },
 {
  "id": 54,
  "title": "Community Tax Certificate (Cedula)",
  "who": "Every inhabitant of the Philippines 18 years old or over who is regularly employed, engaged in business or an occupation, owns real property with an aggregate assessed value of ₱1,000 or more, or is required by law to file an income tax return; corporations may also secure a CTC",
  "steps": [
   "Fill up the CTC information sheet and present your requirements. The office accepts the information sheet and prepares the CTC (about 2 minutes).",
   "Pay the fee and claim your CTC (about 1 minute). The fee is ₱5 basic tax plus ₱1 for every ₱1,000 of gross sales, receipts or income (not more than ₱5,000) for individuals, and ₱500 basic tax plus ₱2 for every ₱5,000 of gross sales or receipts and every ₱5,000 worth of real property owned (not more than ₱10,000) for corporations."
  ]
 },
 {
  "id": 55,
  "title": "Tax Clearance",
  "who": "Any taxpayer, resident or non-resident, who owns real property within Norzagaray, Bulacan",
  "steps": [
   "Submit the complete requirements. The staff interview you (about 5 minutes).",
   "Pay the ₱100 fee. The office accepts the fee and releases your Official Receipt (about 3 minutes).",
   "Wait for the Tax Clearance Certificate. The office accepts your Official Receipt and prepares the Tax Clearance Certificate (about 5 minutes).",
   "Sign and claim your Tax Clearance Certificate (about 2 minutes)."
  ]
 },
 {
  "id": 56,
  "title": "Out-Patient Treatment",
  "who": "All patients who need medical consultation",
  "steps": [
   "Go to the Outpatient Department Record section. Your information is taken and you are given a queue number (about 5 minutes).",
   "Proceed to the OPD consultation room. Your record is accomplished and your vital signs are taken (about 5 minutes), then you are referred to the physician on duty (about 5 minutes). The doctor examines you and prescribes medicines, and requests diagnostic tests if necessary.",
   "If tests are needed, proceed to the cashier. An Official Receipt is issued for the payment.",
   "Bring the request and your Official Receipt to the Laboratory Section, the Radiology Section (X-ray), the ECG or the Ultrasound, as requested (about 10 minutes each). The results are released.",
   "Return to the OPD doctor with your laboratory results and any X-ray or ECG results for final disposition (about 6 minutes).",
   "You are sent home. The doctor prescribes medication and gives instructions (about 5 minutes)."
  ]
 },
 {
  "id": 57,
  "title": "Emergency Services",
  "who": "All patients who need immediate medical treatment and intervention",
  "steps": [
   "Go to the Emergency Room, with one companion or relative of the patient. Staff assist patients using a stretcher or wheelchair (about 2 minutes), take and record vital signs and refer you to the physician on duty (about 5 minutes). The doctor examines you and gives initial orders (about 5 minutes), and the orders and prescription are carried out (about 5 minutes). Diagnostic tests or procedures are requested if necessary.",
   "If payment is needed, proceed to the cashier. An Official Receipt is issued, based on your billing statement.",
   "Present your Official Receipt at the pharmacy (medicines dispensed, about 3 minutes), the Laboratory Section (about 10 minutes), the Radiology Section for X-ray (about 10 minutes), the ECG (about 10 minutes) or the Ultrasound (about 15 minutes), as needed. Results are released.",
   "Return to the Emergency Room. Medicines are administered as ordered, your diagnostic results are evaluated and given an initial interpretation.",
   "If you improve, you are discharged with a prescription and instructions.",
   "If you need further observation and treatment, you are admitted: the admission is notified, admitting orders are issued and you are brought to the ward.",
   "If you need further evaluation and management, you are referred to a higher level facility. A referral letter is prepared and the receiving hospital is contacted (about 5 minutes)."
  ]
 }
]
JSON, true, 512, JSON_THROW_ON_ERROR);

$select = $pdo->prepare('SELECT title, steps, who_may_avail FROM citizens_charter_services WHERE id = :id');
$update = $pdo->prepare('UPDATE citizens_charter_services SET steps = :steps, who_may_avail = :who WHERE id = :id');

$filled = 0;
$skipped = [];
foreach ($rows as $row) {
    $select->execute(['id' => $row['id']]);
    $current = $select->fetch();
    if (!$current || $current['title'] !== $row['title']) {
        $skipped[] = '#' . $row['id'] . ' ' . $row['title'] . ' (not found, or its title was changed)';
        continue;
    }
    if (trim((string) $current['steps']) !== '') {
        $skipped[] = '#' . $row['id'] . ' ' . $row['title'] . ' (already has steps)';
        continue;
    }
    $update->execute([
        'steps' => implode("
", $row['steps']),
        'who' => $row['who'] === '' ? ($current['who_may_avail'] ?: null) : $row['who'],
        'id' => $row['id'],
    ]);
    $filled++;
}

echo "Steps filled in for $filled of " . count($rows) . " services
";
foreach ($skipped as $line) {
    echo "Skipped: $line
";
}
