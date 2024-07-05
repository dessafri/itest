<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Idcardreport extends Admin_Controller
{
    public $load;
    public $session;
    public $lang;
    public $form_validation;
    public $input;
    public $section_m;
    public $student_m;
    public $systemadmin_m;
    public $question_level_m;
    public $question_level_report_m;
    public $teacher_m;
    public $user_m;
    public $data;
    public $uri;
    /*
    | -----------------------------------------------------
    | PRODUCT NAME:     INILABS SCHOOL MANAGEMENT SYSTEM
    | -----------------------------------------------------
    | AUTHOR:            INILABS TEAM
    | -----------------------------------------------------
    | EMAIL:            info@inilabs.net
    | -----------------------------------------------------
    | COPYRIGHT:        RESERVED BY INILABS IT
    | -----------------------------------------------------
    | WEBSITE:            http://iNilabs.net
    | -----------------------------------------------------
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('usertype_m');
        $this->load->model('classes_m');
        $this->load->model('section_m');
        $this->load->model('user_m');
        $this->load->model('systemadmin_m');
        $this->load->model('student_m');
        $this->load->model('teacher_m');
        $this->load->model('schoolyear_m');
        $this->load->model('question_level_m');
        $this->load->model('question_level_report_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('idcardreport', $language);
    }

    protected function rules($usertypeID)
    {
        $rules = array(
            array(
                'field' => 'usertypeID',
                'label' => $this->lang->line('idcardreport_idcard'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            ), array(
                'field' => 'userID',
                'label' => $this->lang->line('idcardreport_user'),
                'rules' => 'trim|xss_clean|numeric',
            ), array(
                'field' => 'type',
                'label' => $this->lang->line('idcardreport_type'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            ), array(
                'field' => 'background',
                'label' => $this->lang->line('idcardreport_background'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            ),
        );

        if ($usertypeID == 3) {
            $rules[] = array(
                'field' => 'classesID',
                'label' => $this->lang->line('idcardreport_class'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            );

            $rules[] = array(
                'field' => 'sectionID',
                'label' => $this->lang->line('idcardreport_section'),
                'rules' => 'trim|xss_clean|greater_than_equal_to[0]',
            );
        }
        return $rules;
    }

    protected function send_pdf_to_mail_rules($usertypeID)
    {
        $rules = array(
            array(
                'field' => 'usertypeID',
                'label' => $this->lang->line('idcardreport_idcard'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            ), array(
                'field' => 'userID',
                'label' => $this->lang->line('idcardreport_user'),
                'rules' => 'trim|xss_clean|numeric',
            ), array(
                'field' => 'type',
                'label' => $this->lang->line('idcardreport_type'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            ), array(
                'field' => 'background',
                'label' => $this->lang->line('idcardreport_background'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            ), array(
                'field' => 'to',
                'label' => $this->lang->line('idcardreport_to'),
                'rules' => 'trim|required|xss_clean|valid_email',
            ), array(
                'field' => 'subject',
                'label' => $this->lang->line('idcardreport_subject'),
                'rules' => 'trim|required|xss_clean',
            ), array(
                'field' => 'message',
                'label' => $this->lang->line('idcardreport_message'),
                'rules' => 'trim|xss_clean',
            ),
        );
        if ($usertypeID == 3) {
            $rules[] = array(
                'field' => 'classesID',
                'label' => $this->lang->line('idcardreport_class'),
                'rules' => 'trim|required|xss_clean|numeric|callback_unique_data',
            );

            $rules[] = array(
                'field' => 'sectionID',
                'label' => $this->lang->line('idcardreport_section'),
                'rules' => 'trim|xss_clean|greater_than_equal_to[0]',
            );
        }
        return $rules;
    }

    public function unique_data($data)
    {
        if ($data != '') {
            if ($data == 0) {
                $this->form_validation->set_message('unique_data', 'The %s field is required.');
                return false;
            }
            return true;
        }
        return true;
    }

    public function getSection()
    {
        $classesID = $this->input->post('classesID');
        if ((int) $classesID !== 0) {
            $sections = $this->section_m->get_order_by_section(array('classesID' => $classesID));
            echo "<option value='0'>" . $this->lang->line("idcardreport_please_select") . "</option>";
            if (inicompute($sections)) {
                foreach ($sections as $section) {
                    echo "<option value='" . $section->sectionID . "'>" . $section->section . "</option>";
                }
            }
        }
    }

    public function getStudentByClass()
    {
        $usertypeID = $this->input->post('usertypeID');
        $classesID = $this->input->post('classesID');
        if (((int) $usertypeID && $usertypeID == 3) && ((int) $classesID && $classesID > 0)) {
            $queryArray['schoolyearID'] = $this->session->userdata('defaultschoolyearID');
            $queryArray['classesID'] = $classesID;
            $users = $this->student_m->get_order_by_student($queryArray);
            if (inicompute($users)) {
                echo "<option value='0'>" . $this->lang->line("idcardreport_please_select") . "</option>";
                foreach ($users as $user) {
                    echo "<option value='" . $user->studentID . "'>" . $user->name . "</option>";
                }
            }
        }
    }

    public function getStudentBySection()
    {
        $usertypeID = $this->input->post('usertypeID');
        $classesID = $this->input->post('classesID');
        $sectionID = $this->input->post('sectionID');
        if (((int) $usertypeID && $usertypeID == 3) && ((int) $classesID && $classesID > 0)) {
            $queryArray['schoolyearID'] = $this->session->userdata('defaultschoolyearID');
            $queryArray['classesID'] = $classesID;
            if ((int) $sectionID && $sectionID > 0) {
                $queryArray['sectionID'] = $sectionID;
            }
            $users = $this->student_m->get_order_by_student($queryArray);
            if (inicompute($users)) {
                echo "<option value='0'>" . $this->lang->line("idcardreport_please_select") . "</option>";
                foreach ($users as $user) {
                    echo "<option value='" . $user->studentID . "'>" . $user->name . "</option>";
                }
            }
        }
    }

    public function getUser()
    {
        $usertypeID = $this->input->post('usertypeID');
        $classesID = $this->input->post('classesID');
        $sectionID = $this->input->post('sectionID');

        if ((int) $usertypeID && ((int) $classesID || $classesID == 0) && ((int) $sectionID || $sectionID == 0)) {
            echo "<option value='0'>" . $this->lang->line("idcardreport_please_select") . "</option>";
            if ($usertypeID == 1) {
                $users = $this->systemadmin_m->get_systemadmin();
                if (inicompute($users)) {
                    foreach ($users as $user) {
                        echo "<option value='" . $user->systemadminID . "'>" . $user->name . "</option>";
                    }
                }
            } elseif ($usertypeID == 2) {
                $users = $this->teacher_m->get_teacher();
                if (inicompute($users)) {
                    foreach ($users as $user) {
                        echo "<option value='" . $user->teacherID . "'>" . $user->name . "</option>";
                    }
                }
            } elseif ($usertypeID == 3) {
                $users = [];
            } elseif ($usertypeID == 4) {
                $users = [];
            } else {
                $users = $this->user_m->get_order_by_user(array('usertypeID' => $usertypeID));
                if (inicompute($users)) {
                    foreach ($users as $user) {
                        echo "<option value='" . $user->userID . "'>" . $user->name . "</option>";
                    }
                }
            }
        }
    }

    private function queryArray($posts)
    {
        $usertypeID = $posts['usertypeID'];
        $classesID = $posts['classesID'];
        $sectionID = $posts['sectionID'];
        $userID = $posts['userID'];

        $queryArray = [];
        if ($usertypeID == 1) {
            if ($userID > 0) {
                $queryArray['systemadminID'] = $userID;
            }
            $users = $this->systemadmin_m->get_order_by_systemadmin($queryArray);
        } elseif ($usertypeID == 2) {
            if ($userID > 0) {
                $queryArray['teacherID'] = $userID;
            }
            $users = $this->teacher_m->get_order_by_teacher($queryArray);
        } elseif ($usertypeID == 3) {
            $queryArray['schoolyearID'] = $this->session->userdata('defaultschoolyearID');
            $queryArray['classesID'] = $classesID;
            if ($sectionID > 0) {
                $queryArray['sectionID'] = $sectionID;
            }
            if ($userID > 0) {
                $queryArray['studentID'] = $userID;
            }
            $users = $this->student_m->get_order_by_student($queryArray);
        } elseif ($usertypeID == 4) {
            $users = [];
        } else {
            $queryArray['usertypeID'] = $usertypeID;
            if ($userID > 0) {
                $queryArray['userID'] = $userID;
            }
            $users = $this->user_m->get_order_by_user($queryArray);
        }
        return $users;
    }

    public function index()
    {
        $this->data['headerassets'] = array(
            'css' => array(
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css',
            ),
            'js' => array(
                'assets/select2/select2.js',
            ),
        );
        $this->data['usertypes'] = $this->usertype_m->get_usertype();
        $users = $this->student_m->get_student();
        $datareport = $this->question_level_report_m->report_subtype();
        $dataReportType = $this->question_level_report_m->report_type();
        $dataEndReport = $this->question_level_report_m->end_report();
        $dataEndReportLimit2 = $this->question_level_report_m->report_type_limit2();
        $types = $this->question_level_m->get_question_level();
        $results = [];
        foreach ($users as $user) {
            $user_data = [
                'name' => $user->name,
                'types' => [],
            ];

            // Loop untuk setiap tipe pertanyaan
            foreach ($types as $type) {
                $value = 0;

                // Loop untuk setiap data laporan
                foreach ($dataReportType as $report) {
                    if ($report['userID'] == $user->studentID && $report['questionLevelID'] == $type->questionLevelID) {
                        $value = $report['value'];
                        break;
                    }
                }

                // Tambahkan data tipe ke dalam user_data
                $user_data['types'][] = [
                    'name' => $type->name,
                    'value' => $value,
                ];
            }

            // Inisialisasi array untuk hasil tes
            $user_data['result_test'] = [];

            // Loop untuk data akhir
            foreach ($types as $type) {
                foreach ($dataEndReportLimit2 as $data) {
                    if ($data['userID'] == $user->studentID && $data['questionLevelID'] == $type->questionLevelID) {
                        $examIDExists = false;
                        foreach ($user_data['result_test'] as &$exam) {
                            if ($exam['examID'] == $data['examID']) {
                                $exam['results'][] = [
                                    'value_tes' => $data['value'],
                                    'questionLevelID' => $data['questionLevelID'],
                                ];
                                $examIDExists = true;
                                break;
                            }
                        }
            
                        // Jika examID belum ada, buat array baru untuk examID tersebut
                        if (!$examIDExists) {
                            $user_data['result_test'][] = [
                                'examID' => $data['examID'],
                                'results' => [
                                    [
                                        'value_tes' => $data['value'],
                                        'questionLevelID' => $data['questionLevelID'],
                                    ],
                                ],
                            ];
                        }
                    }
                }
            }
            
            // Pastikan setiap examID memiliki jumlah results yang sesuai dengan jumlah types
            foreach ($user_data['result_test'] as &$exam) {
                $currentResultsCount = count($exam['results']);
                $requiredResultsCount = count($types);
                if ($currentResultsCount < $requiredResultsCount) {
                    foreach ($types as $type) {
                        $exists = false;
                        foreach ($exam['results'] as $result) {
                            if ($result['questionLevelID'] == $type->questionLevelID) {
                                $exists = true;
                                break;
                            }
                        }
                        if (!$exists) {
                            $exam['results'][] = [
                                'value_tes' => 0,
                                'questionLevelID' => $type->questionLevelID,
                            ];
                        }
                    }
                }
            }
            
            foreach ($dataEndReport as $nilaiakhir) {
                if ($nilaiakhir['userID'] == $user->studentID) {
                    $user_data['nilai_akhir'] = $nilaiakhir['nilai_akhir'];
                    break;
                }
            }

            // Tambahkan user_data ke dalam finalResults
            $results[] = $user_data;
        }
        // var_dump($results);
        //     exit;
        $this->data['subtype'] = $types;
        $this->data['classes'] = $this->classes_m->get_classes();
        $this->data['results'] = $results;
        $this->data["subview"] = "report/idcard/IdcardReportView";
        $this->load->view('_layout_main', $this->data);
    }

    public function getIdcardReport()
    {
        $retArray['status'] = false;
        $retArray['render'] = '';
        if (permissionChecker('idcardreport')) {
            if ($_POST !== []) {
                $usertypeID = $this->input->post('usertypeID');
                $rules = $this->rules($usertypeID);
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == false) {
                    $retArray = $this->form_validation->error_array();
                    $retArray['status'] = false;
                    echo json_encode($retArray);
                    exit;
                } else {
                    $schoolyearID = $this->session->userdata('defaultschoolyearID');
                    $this->data['usertypeID'] = $usertypeID;
                    $this->data['classesID'] = $this->input->post('classesID');
                    $this->data['sectionID'] = $this->input->post('sectionID');
                    $this->data['userID'] = $this->input->post('userID');
                    $this->data['type'] = $this->input->post('type');
                    $this->data['background'] = $this->input->post('background');
                    $this->data['schoolyear'] = $this->schoolyear_m->get_single_schoolyear(array('schoolyearID' => $schoolyearID));
                    $this->data['classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
                    $this->data['sections'] = pluck($this->section_m->get_section(), 'section', 'sectionID');
                    $this->data['usertypes'] = pluck($this->usertype_m->get_usertype(), 'usertype', 'usertypeID');
                    $this->data['idcards'] = $this->queryArray($this->input->post());
                    $retArray['status'] = true;
                    $retArray['render'] = $this->load->view('report/idcard/IdcardReport', $this->data, true);
                    echo json_encode($retArray);
                    exit;
                }
            } else {
                $retArray['status'] = true;
                $retArray['render'] = $this->load->view('report/reporterror', $this->data, true);
                echo json_encode($retArray);
                exit;
            }
        } else {
            $retArray['status'] = true;
            $retArray['render'] = $this->load->view('report/reporterror', $this->data, true);
            echo json_encode($retArray);
            exit;
        }
    }

    public function pdf()
    {
        if (permissionChecker('idcardreport')) {
            $usertypeID = htmlentities((string) escapeString($this->uri->segment(3)));
            $classesID = htmlentities((string) escapeString($this->uri->segment(4)));
            $sectionID = htmlentities((string) escapeString($this->uri->segment(5)));
            $userID = htmlentities((string) escapeString($this->uri->segment(6)));
            $type = htmlentities((string) escapeString($this->uri->segment(7)));
            $background = htmlentities((string) escapeString($this->uri->segment(8)));
            $schoolyearID = $this->session->userdata('defaultschoolyearID');

            if ((int) $usertypeID && ((int) $classesID || $classesID == 0) && ((int) $sectionID || $sectionID == 0) && ((int) $userID || $userID == 0) && (int) $type && (int) $background) {
                $this->data['usertypeID'] = $usertypeID;
                $this->data['classesID'] = $classesID;
                $this->data['sectionID'] = $sectionID;
                $this->data['userID'] = $userID;
                $this->data['type'] = $type;
                $this->data['background'] = $background;
                $this->data['schoolyear'] = $this->schoolyear_m->get_single_schoolyear(array('schoolyearID' => $schoolyearID));

                $this->data['classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
                $this->data['sections'] = pluck($this->section_m->get_section(), 'section', 'sectionID');
                $this->data['usertypes'] = pluck($this->usertype_m->get_usertype(), 'usertype', 'usertypeID');
                $array['usertypeID'] = $usertypeID;
                $array['classesID'] = $classesID;
                $array['sectionID'] = $sectionID;
                $array['userID'] = $userID;
                $this->data['idcards'] = $this->queryArray($array);
                $this->reportPDF('idcardreport.css', $this->data, 'report/idcard/IdcardReportPDF', 'view', 'a4');
            } else {
                $this->data["subview"] = "error";
                $this->load->view('_layout_main', $this->data);
            }
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function send_pdf_to_mail()
    {
        $retArray['status'] = false;
        $retArray['message'] = '';

        if (permissionChecker('idcardreport')) {
            if ($_POST !== []) {
                $to = $this->input->post('to');
                $subject = $this->input->post('subject');
                $message = $this->input->post('message');
                $usertypeID = $this->input->post('usertypeID');
                $classesID = $this->input->post('classesID');
                $sectionID = $this->input->post('sectionID');
                $userID = $this->input->post('userID');
                $type = $this->input->post('type');
                $background = $this->input->post('background');
                $schoolyearID = $this->session->userdata('defaultschoolyearID');
                $rules = $this->send_pdf_to_mail_rules($usertypeID);
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == false) {
                    $retArray = $this->form_validation->error_array();
                    $retArray['status'] = false;
                    echo json_encode($retArray);
                    exit;
                } elseif ((int) $usertypeID && ((int) $classesID || $classesID == 0) && ((int) $sectionID || $sectionID == 0) && ((int) $userID || $userID == 0) && (int) $type && (int) $background) {
                    $this->data['usertypeID'] = $usertypeID;
                    $this->data['classesID'] = $classesID;
                    $this->data['sectionID'] = $sectionID;
                    $this->data['userID'] = $userID;
                    $this->data['type'] = $type;
                    $this->data['background'] = $background;
                    $this->data['schoolyear'] = $this->schoolyear_m->get_single_schoolyear(array('schoolyearID' => $schoolyearID));
                    $this->data['classes'] = pluck($this->classes_m->get_classes(), 'classes', 'classesID');
                    $this->data['sections'] = pluck($this->section_m->get_section(), 'section', 'sectionID');
                    $this->data['usertypes'] = pluck($this->usertype_m->get_usertype(), 'usertype', 'usertypeID');
                    $array['usertypeID'] = $usertypeID;
                    $array['classesID'] = $classesID;
                    $array['sectionID'] = $sectionID;
                    $array['userID'] = $userID;
                    $this->data['idcards'] = $this->queryArray($array);
                    $this->reportSendToMail('idcardreport.css', $this->data, 'report/idcard/IdcardReportPDF', $to, $subject, $message);
                    $retArray['message'] = "Message";
                    $retArray['status'] = true;
                    echo json_encode($retArray);
                    exit;
                } else {
                    $retArray['message'] = $this->lang->line('idcardreport_data_not_found');
                    echo json_encode($retArray);
                    exit;
                }
            } else {
                $retArray['message'] = $this->lang->line('idcardreport_permissionmethod');
                echo json_encode($retArray);
                exit;
            }
        } else {
            $retArray['message'] = $this->lang->line('idcardreport_permission');
            echo json_encode($retArray);
            exit;
        }
    }

}

/* End of file activities.php */
/* Location: .//D/xampp/htdocs/school/mvc/controllers/activities.php */
