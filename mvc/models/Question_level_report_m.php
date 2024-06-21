<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Question_level_report_m extends MY_Model
{

    protected $_table_name = 'question_level_report';
    protected $_primary_key = 'questionLevelReportID';
    protected $_primary_filter = 'intval';
    protected $_order_by = "questionLevelReportID asc";

    public function __construct()
    {
        parent::__construct();
    }

    public function insert_question_level_report($array)
    {
        $error = parent::insert($array);
        return true;
    }
    public function insert_batch_question_level_report($array)
    {
        if (!empty($array)) {
            $this->db->insert_batch('question_level_report', $array); // Replace 'your_table_name' with the actual table name
        }
        return true;
    }

    public function update_question_level($data, $id = null)
    {
        parent::update($data, $id);
        return $id;
    }

    public function delete_question_level($id)
    {
        parent::delete($id);
    }
    public function compute_jawaban($iduser, $idexam)
    {
        $query = "SELECT
    c.levelID AS questionLevelID,
    CASE
        WHEN e.title = 'Atasan' THEN SUM(b.nilaijawaban) * 0.5
        WHEN e.title = 'Bawahan' THEN SUM(b.nilaijawaban) * 0.3
        WHEN e.title = 'Rekanan' THEN SUM(b.nilaijawaban) * 0.2
        ELSE SUM(b.nilaijawaban)
    END AS value,
    d.groupID AS groupID,
    a.userID AS userID
    FROM
        online_exam_user_answer_option a
    JOIN
        question_option b ON a.optionID = b.optionID
    JOIN
        question_bank c ON b.questionID = c.questionBankID
    JOIN
        online_exam d ON a.onlineExamID = d.onlineExamID
    JOIN
        question_group e ON d.groupID = e.questionGroupID
    WHERE
        a.userID = '$iduser'
        AND a.onlineExamID = '$idexam'
    GROUP BY
        c.levelID, d.groupID";
        $result = $this->db->query($query);
        return $result->result_array();
    }
}
