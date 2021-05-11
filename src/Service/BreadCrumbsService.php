<?php

namespace App\Service;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Entity\Link;
use App\Entity\Question;
use App\Entity\Questionnaire;
use Symfony\Component\Security\Core\Security;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * This class organize all breadcrumbs from app.
 */
class BreadCrumbsService
{
    private $bC;

    private $user;

    private $find;

    public function __construct(Breadcrumbs $bC, Security $security, FindEntity $find)
    {
        $this->bC = $bC;
        $this->user = $security->getUser();
        $this->find = $find;
    }

    /**
     * Add home page depends in user type.
     */
    private function userHome(): Breadcrumbs
    {
        switch ($this->user->getRoles()[0]) {
            case 'ROLE_TEACHER':
                $this->bC->addRouteItem('Accueil', 'teacher_show');
                break;
            case 'ROLE_STUDENT':
                $this->bC->addRouteItem('Accueil', 'student_show');
                break;
            default:
                $this->bC->addRouteItem('Accueil', 'user_show');
        }

        return $this->bC;
    }

    /**
     * Beginning for classroom.
     */
    private function classroomStart(string $classroom_id, ?bool $extra): Breadcrumbs
    {
        $this->userHome()->addRouteItem('Classe',
            'classroom_show', [
                'id' => $classroom_id,
                'extra' => $extra,
            ]
        );

        return $this->bC;
    }

    /**
     * Begining for questionnaires.
     */
    private function questionnaireStart(
        ?string $classroom_id,
        ?string $lesson_id,
        ?string $list,
        ?string $lonely
    ): Breadcrumbs {
        if (isset($classroom_id) && $list) {
            $this->classroomStart($classroom_id, null)
                ->addRouteItem('Créer un Module', 'lesson_new', ['classroom_id' => $classroom_id])
                ->addRouteItem('Modules', 'lesson_index', [
                    'classroom_id' => $classroom_id,
                    'list' => $list,
                ])
                ->addRouteItem('Module', 'lesson_show', [
                    'id' => $lesson_id,
                    'classroom_id' => $classroom_id,
                    'list' => $list,
                ])
            ;
        } elseif (!isset($classroom_id) && $list) {
            $this->userHome()
                ->addRouteItem('Modules', 'lesson_index', [
                    'list' => $list,
                    'lonely' => $lonely,
                ])
                ->addRouteItem('Module', 'lesson_show', [
                    'id' => $lesson_id,
                    'list' => $list,
                    'lonely' => $lonely,
                ])
            ;
        } elseif ($lonely) {
            $this->classroomStart($classroom_id, null)->addRouteItem('Module', 'lesson_show', [
                'id' => $lesson_id,
                'classroom_id' => $classroom_id,
                'lonely' => $lonely,
            ]);
        } else {
            $this->userHome()->addRouteItem('Activités', 'questionnaire_index');
        }

        return $this->bC;
    }

    /**
     * Handling list of users breadcrumbs.
     */
    public function bcListUsers(string $type, ?bool $listProfileEdit): Breadcrumbs
    {
        $this->bC->addRouteItem('Accueil', 'user_show');

        if ('teachers' === $type) {
            $list = 'Formateurs·rices';
            $route = 'teacher_edit_profile';
        } else {
            $list = 'Apprenants·es';
            $route = 'student_edit_profile';
        }

        $this->bC->addRouteItem($list, 'user_list', [
            'type' => $type,
        ]);
        if ($listProfileEdit) {
            $this->bC->addRouteItem('Éditer profil', $route);
        }

        return $this->bC;
    }

    /**
     * Handling profiles breadcrumbs.
     */
    public function bcProfile(?bool $edit): Breadcrumbs
    {
        switch ($this->user->getRoles()[0]) {
            case 'ROLE_TEACHER':
                $this->bC
                    ->addRouteItem('Accueil', 'teacher_show')
                    ->addRouteItem('Profile', 'teacher_profile')
                ;
                if ($edit) {
                    $this->bC->addRouteItem('Editer Profile', 'teacher_edit_profile');
                }
                break;
            case 'ROLE_STUDENT':
                $this->bC
                    ->addRouteItem('Accueil', 'student_show')
                    ->addRouteItem('Profile', 'student_profile')
                ;
                if ($edit) {
                    $this->bC->addRouteItem('Editer Profile', 'student_edit_profile');
                }
                break;
            default:
                $this->bC
                    ->addRouteItem('Accueil', 'user_show')
                    ->addRouteItem('Profile', 'user_profile')
                ;
                if ($edit) {
                    $this->bC->addRouteItem('Editer Profile', 'user_edit_profile');
                }
        }

        return $this->bC;
    }

    /**
     * Handling avatar breadcrumbs.
     */
    public function bcAvatar(): Breadcrumbs
    {
        $this->bcProfile(false, null);
        switch ($this->user->getRoles()[0]) {
            case 'ROLE_TEACHER':
                $this->bC->addRouteItem('Avatar', 'teacher_edit_avatar');
                break;
            case 'ROLE_STUDENT':
                $this->bC->addRouteItem('Avatar', 'student_edit_avatar');
                break;
            default:
                $this->bC->addRouteItem('Avatar', 'user_edit_avatar');
        }

        return $this->bC;
    }

    /**
     * Handling all classroom breadcrumbs.
     */
    public function bcClassroom(?Classroom $classroom, string $methode): Breadcrumbs
    {
        switch ($methode) {
            case 'show':
                $this->classroomStart($classroom->getId(), null);
                break;
            case 'new':
                $this->bC->addRouteItem('Accueil', 'user_show');
                $this->bC->addRouteItem('Créer une classe', 'classroom_new')
                ;
                break;
            case 'edit':
                $this->bC->addRouteItem('Accueil', 'user_show');
                $this->bC->addRouteItem('Éditer une classe', 'classroom_edit', ['id' => $classroom->getId()])
                ;
                break;
        }

        return $this->bC;
    }

    /**
     * Handling all links breadcrumbs.
     */
    public function bcLink(?Link $link, string $methode, ?string $classroom_id, ?bool $extra): Breadcrumbs
    {
        if (isset($classroom_id) || $extra) {
            $this->classroomStart($classroom_id, $extra);
        } else {
            $this->userHome()->addRouteItem('Liens', 'link_index');
        }

        switch ($methode) {
            case 'index':
                if (isset($classroom_id)) {
                    $this->bC
                        ->addRouteItem('Créer une lien', 'link_new', ['classroom_id' => $classroom_id])
                        ->addRouteItem('Liens', 'link_index')
                    ;
                }
                break;
            case 'new':
                $this->bC->addRouteItem('Créer une lien', 'link_new');
                break;
            case 'edit':
                if (isset($classroom_id) && !$extra) {
                    $this->bC
                        ->addRouteItem('Créer une lien', 'link_new', ['classroom_id' => $classroom_id])
                        ->addRouteItem('Liens', 'link_index', ['classroom_id' => $classroom_id])
                        ->addRouteItem('Editer une lien', 'link_edit', ['id' => $link->getId()])
                    ;
                } else {
                    $this->bC->addRouteItem('Editer une lien', 'link_edit', ['id' => $link->getId()]);
                }
                break;
        }

        return $this->bC;
    }

    /**
     * Handling all lessons breadcrumbs.
     */
    public function bcLesson(
        ?Lesson $lesson,
        string $methode,
        ?string $classroom_id,
        ?bool $list,
        ?bool $lonely,
        ?bool $extra
    ): Breadcrumbs {
        if (isset($classroom_id) || $extra) {
            $this->classroomStart($classroom_id, $extra);
        } else {
            $this->userHome()->addRouteItem('Modules', 'lesson_index');
        }

        switch ($methode) {
            case 'index':
                if (isset($classroom_id)) {
                    $this->bC
                        ->addRouteItem('Créer un module', 'lesson_new', ['classroom_id' => $classroom_id])
                        ->addRouteItem('Modules', 'lesson_index')
                    ;
                }
                break;
            case 'new':
                $this->bC->addRouteItem('Créer un module', 'lesson_new');
                break;
            case 'show':
                if ($list && isset($classroom_id)) {
                    $this->bC
                        ->addRouteItem('Créer un module', 'lesson_new', ['classroom_id' => $classroom_id])
                        ->addRouteItem('Modules', 'lesson_index', [
                            'classroom_id' => $classroom_id,
                            'list' => $list,
                        ])
                        ->addRouteItem('Module', 'lesson_show', ['id' => $lesson->getId()])
                    ;
                } elseif ($lonely) {
                    $this->bC->addRouteItem('Module', 'lesson_show', ['id' => $lesson->getId()]);
                } else {
                    $this->bC
                        ->addRouteItem('Module', 'lesson_show', ['id' => $lesson->getId()])
                    ;
                }
                break;
            case 'edit':
                if (isset($classroom_id) && !$extra) {
                    $this->bC
                        ->addRouteItem('Créer un module', 'lesson_new', ['classroom_id' => $classroom_id])
                        ->addRouteItem('Modules', 'lesson_index', [
                            'classroom_id' => $classroom_id,
                            'list' => $list,
                        ])
                        ->addRouteItem('Éditer un module', 'lesson_edit', ['id' => $lesson->getId()])
                    ;
                } else {
                    $this->bC->addRouteItem('Éditer un module', 'lesson_edit', ['id' => $lesson->getId()]);
                }
                break;
        }

        return $this->bC;
    }

    public function bcQuestionnaire(
        ?Questionnaire $questionnaire,
        string $methode,
        ?string $classroom_id,
        ?string $lesson_id,
        ?bool $list,
        ?bool $lonely,
        ?bool $extra
    ): Breadcrumbs {
        $this->questionnaireStart($classroom_id, $lesson_id, $list, $lonely);

        switch ($methode) {
            case 'index':
                if ($list) {
                    $this->bC
                        ->addRouteItem('Créer une Activité', 'questionnaire_new', [
                            'list' => $list,
                            'lesson_id' => $lesson_id,
                        ])
                        ->addRouteItem('Activités', 'questionnaire_index')
                    ;
                } elseif ($lonely) {
                    $this->bC
                        ->addRouteItem('Créer une Activité', 'questionnaire_new', [
                            'lesson_id' => $lesson_id,
                            'classroom_id' => $classroom_id,
                            'lonely' => $lonely,
                        ])
                        ->addRouteItem('Activités', 'questionnaire_index')
                    ;
                }
                break;
            case 'new':
                $this->bC->addRouteItem('Créer une Activité', 'questionnaire_new');
                break;
            case 'edit':
                $this->bC->addRouteItem('Editer une Activité', 'questionnaire_edit', ['id' => $questionnaire->getId()]);
                break;
            case 'show':
                if ($lonely && !isset($extra)) {
                    $this->bC
                        ->addRouteItem('Créer une Activité', 'questionnaire_new', [
                            'lesson_id' => $lesson_id,
                            'classroom_id' => $classroom_id,
                            'lonely' => $lonely,
                        ])
                        ->addRouteItem('Activités', 'questionnaire_index', [
                            'lesson_id' => $lesson_id,
                            'classroom_id' => $classroom_id,
                            'lonely' => $lonely,
                        ])
                        ->addRouteItem('Activité', 'questionnaire_show', ['id' => $questionnaire->getId()])
                    ;
                } elseif ($extra && !isset($classroom_id)) {
                    $this->bC
                        ->addRouteItem('Créer une Activité', 'questionnaire_new', [
                            'lesson_id' => $lesson_id,
                            'list' => $list,
                        ])
                        ->addRouteItem('Activités', 'questionnaire_index', [
                            'lesson_id' => $lesson_id,
                            'list' => $list,
                            'extra' => $extra,
                        ])
                        ->addRouteItem('Activité', 'questionnaire_show', ['id' => $questionnaire->getId()])
                    ;
                } else {
                    $this->bC->addRouteItem('Activité', 'questionnaire_show', [
                        'id' => $questionnaire->getId(),
                        'list' => $list,
                        'lesson_id' => $lesson_id,
                        'classroom_id' => $classroom_id,
                        'extra' => $extra,
                        'lonely' => $lonely,
                    ]);
                }
                break;
            case 'play':
                if ($lonely && !isset($extra)) {
                    $this->bC
                        ->addRouteItem('Créer une Activité', 'questionnaire_new', [
                            'lesson_id' => $lesson_id,
                            'classroom_id' => $classroom_id,
                            'lonely' => $lonely,
                        ])
                        ->addRouteItem('Activités', 'questionnaire_index', [
                            'lesson_id' => $lesson_id,
                            'classroom_id' => $classroom_id,
                            'lonely' => $lonely,
                        ])
                        ->addRouteItem('Activité', 'questionnaire_show', [
                            'id' => $questionnaire->getId(),
                            'lesson_id' => $lesson_id,
                            'classroom_id' => $classroom_id,
                            'lonely' => $lonely,
                        ])
                        ->addRouteItem('Realiser une Activité', 'questionnaire_play', ['id' => $questionnaire->getId()])
                    ;
                } elseif ($extra && !isset($classroom_id)) {
                    $this->bC
                        ->addRouteItem('Créer une Activité', 'questionnaire_new', [
                            'lesson_id' => $lesson_id,
                            'list' => $list,
                        ])
                        ->addRouteItem('Activités', 'questionnaire_index', [
                            'lesson_id' => $lesson_id,
                            'list' => $list,
                            'extra' => $extra,
                        ])
                        ->addRouteItem('Activité', 'questionnaire_show', [
                            'id' => $questionnaire->getId(),
                            'lesson_id' => $lesson_id,
                            'list' => $list,
                            'extra' => $extra,
                        ])
                        ->addRouteItem('Realiser une Activité', 'questionnaire_play', ['id' => $questionnaire->getId()])
                    ;
                // } if (isset($classroom_id) && $list) {
                } else {
                    $this->bC
                        ->addRouteItem('Activité', 'questionnaire_show', [
                            'id' => $questionnaire->getId(),
                            'lesson_id' => $lesson_id,
                            'classroom_id' => $classroom_id,
                            'lonely' => $lonely,
                            'list' => $list,
                            'extra' => $extra,
                        ])
                        ->addRouteItem('Realiser une Activité', 'questionnaire_play', ['id' => $questionnaire->getId()])
                    ;
                }
                break;
            break;
        }

        return $this->bC;
    }

    public function bcQuestion(
        ?Question $question,
        string $methode,
        ?string $classroom_id,
        ?string $lesson_id,
        ?string $questionnaire_id,
        ?bool $list,
        ?bool $lonely,
        ?bool $extra
    ): Breadcrumbs {
        $questionnaire = $this->find->findQuestionnaire($questionnaire_id);
        $this->bcQuestionnaire($questionnaire, 'show', $classroom_id, $lesson_id, $list, $lonely, $extra);

        if ('edit' === $methode) {
            $this->bC->addRouteItem('Editer une Question', 'question_edit', ['id' => $question->getId()]);
        } else {
            $this->bC->addRouteItem('Créer une Question', 'question_new');
        }

        return $this->bC;
    }
}
