<?php

/**
 * Cancer classifications.
 *
 * Each row has all three levels filled:
 *  - general  : General Classification
 *  - parent   : Parent Classification (same as general when there is no separate parent)
 *  - specific : Specific Classification
 */

return [
    // Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Lymphoid Leukemia', 'specific' => 'Precursor cell leukemia'],
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Lymphoid Leukemia', 'specific' => 'Mature B cell leukemia'],
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Lymphoid Leukemia', 'specific' => 'Mature T and NK cell leukemia'],
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Lymphoid Leukemia', 'specific' => 'Lymphoid Leukemia, NOS'],
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'specific' => 'Acute Myeloid Leukemia'],
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'specific' => 'Chronic myeloproliferative disease'],
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'specific' => 'Myelodysplastic Syndrome and other myeloproliferative disease'],
    ['general' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'parent' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease', 'specific' => 'Unspecified and other specified leukemias'],

    // Lymphoma and reticuloendothelial neoplasms
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Lymphoma and reticuloendothelial neoplasms', 'specific' => 'Hodgkin lymphoma'],
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Non-Hodgkin Lymphoma', 'specific' => 'Precursor cell lymphoma'],
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Non-Hodgkin Lymphoma', 'specific' => 'Mature B-cell lymphoma'],
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Non-Hodgkin Lymphoma', 'specific' => 'Mature T-cell and NK-cell lymphoma'],
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Non-Hodgkin Lymphoma', 'specific' => 'Non-Hodgkin Lymphoma, Not otherwise specified'],
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Lymphoma and reticuloendothelial neoplasms', 'specific' => 'Burkitt lymphoma'],
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Lymphoma and reticuloendothelial neoplasms', 'specific' => 'Miscellaneous lymphoreticular neoplasms'],
    ['general' => 'Lymphoma and reticuloendothelial neoplasms', 'parent' => 'Lymphoma and reticuloendothelial neoplasms', 'specific' => 'Unspecified lymphomas'],

    // CNS and Miscellaneous Intracranial and Intraspinal Neoplasms
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Ependymoma and choroid plexus tumors', 'specific' => 'Ependymoma'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Ependymoma and choroid plexus tumors', 'specific' => 'Choroid Plexus Tumors'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'specific' => 'Astrocytomas'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Intracranial and Intraspinal Embryonal Tumors', 'specific' => 'Medulloblastoma'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Intracranial and Intraspinal Embryonal Tumors', 'specific' => 'Primitive Neuroectodermal Tumor'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Intracranial and Intraspinal Embryonal Tumors', 'specific' => 'Medulloepithelioma'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Intracranial and Intraspinal Embryonal Tumors', 'specific' => 'Atypical Teratoid/Rhabdoid Tumor'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Gliomas', 'specific' => 'Oligodendroglioma'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Gliomas', 'specific' => 'Mixed and Unspecified Gliomas'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Gliomas', 'specific' => 'Neuroepithelial Glial Tumors of Uncertain Origin'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Specified Intracranial and Intraspinal Neoplasms', 'specific' => 'Pituitary Adenomas and Carcinomas'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Specified Intracranial and Intraspinal Neoplasms', 'specific' => 'Tumors of the Sellar Region (Craniopharyngioma)'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Specified Intracranial and Intraspinal Neoplasms', 'specific' => 'Pineal Parenchymal Tumors'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Specified Intracranial and Intraspinal Neoplasms', 'specific' => 'Neuronal and Mixed Neuronal-Glial Tumors'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'Other Specified Intracranial and Intraspinal Neoplasms', 'specific' => 'Meningiomas'],
    ['general' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'parent' => 'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms', 'specific' => 'Unspecified Intracranial and Intraspinal Neoplasms'],

    // Neuroblastoma and Other Peripheral Nervous Cell Tumors
    ['general' => 'Neuroblastoma and Other Peripheral Nervous Cell Tumors', 'parent' => 'Neuroblastoma and Other Peripheral Nervous Cell Tumors', 'specific' => 'Neuroblastoma and Ganglioneuroma'],
    ['general' => 'Neuroblastoma and Other Peripheral Nervous Cell Tumors', 'parent' => 'Neuroblastoma and Other Peripheral Nervous Cell Tumors', 'specific' => 'Other Peripheral Nervous Cell Tumors'],

    // Retinoblastoma
    ['general' => 'Retinoblastoma', 'parent' => 'Retinoblastoma', 'specific' => 'Retinoblastoma'],

    // Renal Tumors
    ['general' => 'Renal Tumors', 'parent' => 'Nephroblastoma or Other Nonepithelial Renal Tumors', 'specific' => 'Nephroblastoma'],
    ['general' => 'Renal Tumors', 'parent' => 'Nephroblastoma or Other Nonepithelial Renal Tumors', 'specific' => 'Rhabdoid Renal Tumor'],
    ['general' => 'Renal Tumors', 'parent' => 'Nephroblastoma or Other Nonepithelial Renal Tumors', 'specific' => 'Kidney Sarcomas'],
    ['general' => 'Renal Tumors', 'parent' => 'Renal Tumors', 'specific' => 'Renal Carcinomas'],
    ['general' => 'Renal Tumors', 'parent' => 'Renal Tumors', 'specific' => 'Unspecified Malignant Renal Tumors'],

    // Hepatic Tumors
    ['general' => 'Hepatic Tumors', 'parent' => 'Hepatoblastoma and Mesenchymal Tumor of Liver', 'specific' => 'Hepatoblastoma'],
    ['general' => 'Hepatic Tumors', 'parent' => 'Hepatoblastoma and Mesenchymal Tumor of Liver', 'specific' => 'Rhabdoid Hepatic Tumor'],
    ['general' => 'Hepatic Tumors', 'parent' => 'Hepatoblastoma and Mesenchymal Tumor of Liver', 'specific' => 'Embryonal Sarcoma of the Liver'],
    ['general' => 'Hepatic Tumors', 'parent' => 'Hepatic Tumors', 'specific' => 'Hepatic Carcinoma'],
    ['general' => 'Hepatic Tumors', 'parent' => 'Hepatic Tumors', 'specific' => 'Unspecified Malignant Hepatic Tumors'],

    // Malignant Bone Tumors
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Malignant Bone Tumors', 'specific' => 'Osteosarcoma'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Malignant Bone Tumors', 'specific' => 'Chondrosarcoma'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Ewing Tumors and Related Sarcomas of Bone', 'specific' => 'Ewing Sarcoma and Askin Tumor of Bone'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Ewing Tumors and Related Sarcomas of Bone', 'specific' => 'Peripheral Neuroectodermal Tumor of Bone'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Other Specified Malignant Bone Tumors', 'specific' => 'Malignant Fibrous Neoplasms of Bone'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Other Specified Malignant Bone Tumors', 'specific' => 'Malignant Chordomas'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Other Specified Malignant Bone Tumors', 'specific' => 'Odontogenic Malignant Tumors'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Other Specified Malignant Bone Tumors', 'specific' => 'Miscellaneous Malignant Bone Tumors'],
    ['general' => 'Malignant Bone Tumors', 'parent' => 'Malignant Bone Tumors', 'specific' => 'Other Unspecified Malignant Bone Tumors'],

    // Soft Tissue and Other Extraosseous Sarcomas
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Soft Tissue and Other Extraosseous Sarcomas', 'specific' => 'Rhabdomyosarcomas'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Fibrosarcoma and Peripheral Nerve Sheath Tumors and Other Fibrous Neoplasms', 'specific' => 'Fibroblastic and Myoblastic Tumors'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Fibrosarcoma and Peripheral Nerve Sheath Tumors and Other Fibrous Neoplasms', 'specific' => 'Nerve Sheath Tumor'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Fibrosarcoma and Peripheral Nerve Sheath Tumors and Other Fibrous Neoplasms', 'specific' => 'Other Fibromatous Neoplasms'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Soft Tissue and Other Extraosseous Sarcomas', 'specific' => 'Kaposi Sarcoma'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Ewing Sarcoma and Askin Tumor of Soft Tissue'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Peripheral Neuroectodermal Tumor of Soft Tissue'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Extrarenal Extrahepatic Rhabdoid Tumor'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Liposarcoma'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Fibrohistiocytic Tumors'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Leiomyosarcoma'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Synovial Sarcoma'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Blood Vessel Tumors'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Osseous and Chondromatous Neoplasms of Soft Tissues'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Alveolar Soft Part Sarcoma'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Other Specified Soft Tissue Sarcomas', 'specific' => 'Miscellaneous Soft Tissue Sarcomas'],
    ['general' => 'Soft Tissue and Other Extraosseous Sarcomas', 'parent' => 'Soft Tissue and Other Extraosseous Sarcomas', 'specific' => 'Unspecified Soft Tissue Sarcomas'],

    // Germ Cell Tumors, Trophoblastic Tumors and Neoplasms
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Intracranial and Intraspinal Germ Cell Tumors', 'specific' => 'Intracranial and Intraspinal Germinomas'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Intracranial and Intraspinal Germ Cell Tumors', 'specific' => 'Intracranial and Intraspinal Teratomas'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Intracranial and Intraspinal Germ Cell Tumors', 'specific' => 'Intracranial and Intraspinal Embryonal Carcinomas'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Intracranial and Intraspinal Germ Cell Tumors', 'specific' => 'Intracranial and Intraspinal Yolk Sac Tumor'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Intracranial and Intraspinal Germ Cell Tumors', 'specific' => 'Intracranial and Intraspinal Choriocarcinoma'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Intracranial and Intraspinal Germ Cell Tumors', 'specific' => 'Intracranial and Intraspinal Tumors of Mixed Forms'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Extracranial and Extragonadal Sites', 'specific' => 'Malignant Germinomas of Extracranial and Extragonadal Sites'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Extracranial and Extragonadal Sites', 'specific' => 'Malignant Teratomas of Extracranial and Extragonadal Sites'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Extracranial and Extragonadal Sites', 'specific' => 'Embryonal Carcinoma of Extracranial and Extragonadal Sites'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Extracranial and Extragonadal Sites', 'specific' => 'Yolk Sac Tumor of Extracranial and Extragonadal Sites'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Extracranial and Extragonadal Sites', 'specific' => 'Choriocarcinomas of Extracranial and Extragonadal Sites'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Extracranial and Extragonadal Sites', 'specific' => 'Other/Unspecified Malignant Germ Cell Tumor of Extracranial and Extragonadal Sites'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Gonadal Germ Cell Tumors', 'specific' => 'Malignant Gonadal Germinomas'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Gonadal Germ Cell Tumors', 'specific' => 'Malignant Gonadal Teratomas'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Gonadal Germ Cell Tumors', 'specific' => 'Gonadal Embryonal Carcinomas'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Gonadal Germ Cell Tumors', 'specific' => 'Gonadal Yolk Sac Tumor'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Gonadal Germ Cell Tumors', 'specific' => 'Gonadal Choriocarcinoma'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Gonadal Germ Cell Tumors', 'specific' => 'Malignant Gonadal Tumors of Mixed Forms'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Malignant Gonadal Germ Cell Tumors', 'specific' => 'Malignant Gonadal Gonadoblastoma'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'specific' => 'Gonadal Carcinomas'],
    ['general' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'parent' => 'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms', 'specific' => 'Other and Unspecified Malignant Gonadal Tumors'],

    // Other Malignant Epithelial Neoplasms and Malignant Melanomas
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Adrenocortical Carcinomas'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Thyroid Carcinoma'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Nasopharyngeal Carcinoma'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Malignant Melanoma'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Skin Carcinoma'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Other and Unspecified Carcinoma'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of salivary glands'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of colon and rectum'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of appendix'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of lung'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of thymus'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of breast'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of cervix uteri'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of bladder'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of eye'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of other specified sites'],
    ['general' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'parent' => 'Other Malignant Epithelial Neoplasms and Malignant Melanomas', 'specific' => 'Carcinoma of unspecified sites'],

    // Other and Unspecified Malignant Neoplasms
    ['general' => 'Other and Unspecified Malignant Neoplasms', 'parent' => 'Other Specified Malignant Tumors', 'specific' => 'Malignant Gastrointestinal Stromal Tumor'],
    ['general' => 'Other and Unspecified Malignant Neoplasms', 'parent' => 'Other Specified Malignant Tumors', 'specific' => 'Pancreatoblastoma'],
    ['general' => 'Other and Unspecified Malignant Neoplasms', 'parent' => 'Other Specified Malignant Tumors', 'specific' => 'Pulmonary blastoma and pleuropulmonary blastoma'],
    ['general' => 'Other and Unspecified Malignant Neoplasms', 'parent' => 'Other Specified Malignant Tumors', 'specific' => 'Other complex mixed and stromal neoplasms'],
    ['general' => 'Other and Unspecified Malignant Neoplasms', 'parent' => 'Other Specified Malignant Tumors', 'specific' => 'Mesothelioma'],
    ['general' => 'Other and Unspecified Malignant Neoplasms', 'parent' => 'Other Specified Malignant Tumors', 'specific' => 'Other specified malignant tumors'],
    ['general' => 'Other and Unspecified Malignant Neoplasms', 'parent' => 'Other Specified Malignant Tumors', 'specific' => 'Other unspecified malignant tumors'],
];
