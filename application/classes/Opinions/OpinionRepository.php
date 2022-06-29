<?php

namespace Application\Opinions;

use Application;
use Core;
use OpinionsDataHelper;
use OpinionsSQLHelper;

class OpinionRepository
{
    use Application\Traits\Constants;

    const ERRORS = [
        'EMPTY_HEADING' => 'Вы не написали заголовок',
        'EMPTY_REGION' => 'Вы не указали регион размещения',
        'BAD_EMAIL' => 'Вы неправильно указали электронную почту',
        'EMPTY_AUTHOR' => 'Вы не указали ваше имя',
        'LEGAL_DISAGREE' => 'Вы не согласились с обработкой персональных данных',
        'MIN_TEXT_LENGTH' => 'Текст объявления не должен быть короче 15 слов',
        'HEADING_EXISTS' => 'Заголовок вашего объявления уже есть в нашей базе',
        'TEXT_EXISTS' => 'Текст вашего объявления уже есть в нашей базе',
        'PHOTO_FORMAT' => 'Ошибка с фото: принимается только JPG и до 2.5 Мб',
        'MESSAGE_REMOVED' => 'К сожалению; такого объявления больше не существует (удалено)',
        'GAME_FORMAT' => 'Ошибка с файлом игры: принимаются только SWF файлы',
        'EMPTY_PHONE' => 'Укажите номер телефона',
        'SPAM_HEADING' => 'Спам в заголовке',
        'SPAM_TEXT' => 'Спам в тексте'
    ];

    protected $id_opinion;
    protected $id_top;
    protected $firm_data = [];

    public function __construct()
    {
        $this->id_top = $_REQUEST['id_top'] ?? 0;
    }

    protected function fix($data, $answer = false)
    {
        if (!$answer) {
            $data['heading'] = OpinionsDataHelper::fixHeading($data['heading']);
        }
        $data['text'] = OpinionsDataHelper::fixText($data['text']);
        $data['author'] = OpinionsDataHelper::fixAuthor($data['author']);

        return $data;
    }

    protected function read($input, $answer = false)
    {
        $data = [];

        // Данные прибывают в utf-8!
        if (!$answer) {
            $data['heading'] = $input['heading'] ?? '';
        }
        $data['text'] = $input['text'] ?? '';
        $data['author'] = $input['author'] ?? '';
        $data['social_serialized'] = $input['social_serialized'] ?? '';
        $data['contact'] = $input['contact'] ?? '';
        $data['legal'] = (int)($input['legal'] ?? 0);
        $data['image'] = $input['image'] ?? '';

        $data = OpinionsDataHelper::convertUTF8toWin($data);

        return $data;
    }

    protected function validate($data, $answer = false)
    {
        if (!$answer && !$data['heading']) {
            return self::ERRORS['EMPTY_HEADING'];
        }
        if (strlen($data['text']) < self::$CONST['OPINIONS']['MIN_TEXT_LENGTH']) {
            return self::ERRORS['MIN_TEXT_LENGTH'];
        }
        if (!$data['author']) {
            return self::ERRORS['EMPTY_AUTHOR'];
        }
        if (!$data['legal']) {
            return self::ERRORS['LEGAL_DISAGREE'];
        }
        if (!$answer && OpinionsDataHelper::spam($data['heading'])) {
            return self::ERRORS['SPAM_HEADING'];
        }

        if (OpinionsDataHelper::spam($data['text'])) {
            return self::ERRORS['SPAM_TEXT'];
        }
        # disable existing checking
        /*
        if (!$answer && $this->headingExists($data['heading'])) {
            return self::ERRORS['HEADING_EXISTS'];
        }
        if ($this->textExists($data['text'])) {
            return self::ERRORS['TEXT_EXISTS'];
        }
        */

        return '';
    }

    protected function headingExists($heading)
    {
        $result = Application::$db->query(
            OpinionsSQLHelper::sel_tema_exists(),
            [
                ':tema' => $heading,
                ':id_top' => $this->id_top
            ]
        );
        if ($result->numRows()) {
            return true;
        }

        $heading = OpinionsDataHelper::cleanText($heading);

        $result = Application::$db->query(
            OpinionsSQLHelper::sel_last_tema_by_ip(),
            [
                ':remote_addr' => Core::$user_ip
            ]
        );
        while ($row = $result->fetchAssocArray()) {
            $var = OpinionsDataHelper::cleanText($row['var']);
            $diff = OpinionsDataHelper::diffText($heading, $var);
            $diff = round($diff);
            if ($diff > self::$CONST['OPINIONS']['TEXT_DIFF_PERCENT']) {
                return true;
            }
        }

        return false;
    }

    protected function textExists($text)
    {
        $text = OpinionsDataHelper::cleanText($text);

        if ($this->id_opinion > 0) {
            $result = Application::$db->query(
                OpinionsSQLHelper::sel_text_exists(),
                [
                    ':text' => $text,
                    ':tema_id' => $this->id_opinion
                ]
            );
            if ($result->numRows()) {
                return true;
            }
        }

        $result = Application::$db->query(
            OpinionsSQLHelper::sel_last_texts_by_ip(),
            [
                ':remote_addr' => Core::$user_ip
            ]
        );
        while ($row = $result->fetchAssocArray()) {
            $var = OpinionsDataHelper::cleanText($row['var']);
            $diff = OpinionsDataHelper::diffText($text, $var);
            $diff = round($diff, 2);
            if ($diff > self::$CONST['OPINIONS']['TEXT_DIFF_PERCENT']) {
                return true;
            }
        }

        return false;
    }

    private function insert($data, $answer = false)
    {
        if (!$answer) {
            Application::$db->query(
                OpinionsSQLHelper::ins_tema(),
                [
                    ':id_razdel' => self::$CONST['OPINIONS']['ID_SECTION'],
                    ':id_top' => $this->id_top,
                    ':var' => $data['heading'],
                    ':username' => Application::$db->escape($data['author']),
                    ':moder' => 1,
                    ':id_raion' => $this->firm_data['ID_KARTA'],
                    ':id_firm' => $this->firm_data['id'],
                    ':id_okrug' => $this->firm_data['ID_OKRUG_FIRMA'],
                    ':id_rubr' => $this->firm_data['ID_RUBR'],
                    ':ip_sozd' => Application::$db->escape(Core::$user_ip)
                ]
            );

            $heading_id = Application::$db->lastInsertId();
        } else {
            $heading_id = $this->id_opinion;
        }

        Application::$db->query(
            OpinionsSQLHelper::ins_text(),
            [
                ':id_razdel' => self::$CONST['OPINIONS']['ID_SECTION'],
                ':id_tema' => (int)$heading_id,
                ':id_top' => (int)$this->id_top,
                ':var' => Application::$db->escape($data['text']),
                ':username' => Application::$db->escape($data['author']),
                ':social_info' => Application::$db->escape($data['social_serialized']),
                ':kontakt' => Application::$db->escape($data['contact']),
                ':moder' => 1,
                ':ip' => Application::$db->escape(Core::$user_ip)
            ]
        );

        $text_id = Application::$db->lastInsertId();

        if (!$answer) {
            Application::$db->query(
                OpinionsSQLHelper::upd_text_sozd(),
                [
                    ':id_text_sozd' => $text_id,
                    ':id' => (int)$heading_id
                ]
            );
        }

        return $text_id;
    }

    // Добавление отзыва
    public function add($answer = false)
    {
        $data = $this->read($_POST, $answer);
        $data = $this->fix($data, $answer);

        $error = $this->validate($data, $answer);
        if ($error) {
            return $error;
        }

        $text_id = $this->insert($data, $answer);

        if (!empty($data['image'])) {
            try {
                $image = \Image::createFromDataURL($data['image']);
                if (!empty($image)) {
                    Application::$db->query(
                        OpinionsSQLHelper::ins_image(),
                        [
                            ':opinion_id' => $text_id,
                            ':image_id' => $image['id']
                        ]
                    );
                }
            } catch (\Exception $e) {
                // without image
            }
        }

        return $text_id;
    }

    /**
     * @param mixed $id_opinion
     */
    public function setIdOpinion($id_opinion)
    {
        $this->id_opinion = $id_opinion;
    }

    /**
     * @param array $firm_data
     */
    public function setFirmData(array $firm_data)
    {
        $this->firm_data = $firm_data;
    }
}