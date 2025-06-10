<?php

namespace App\Service\Entity\Account;

use App\Entity\Account\Account;
use App\Exception\CannotExecuteException;
use App\Form\Admin\Account\AccountType;
use App\Repository\Account\AccountRepository;
use App\Service\Entity\AbstractService;
use App\Service\Entity\Traits\LoggingTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountService extends AbstractService
{
    use LoggingTrait;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AccountRepository $repository,
        private readonly Security $security,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @return Account
     */
    public function createNewEntity(): Account
    {
        return new Account();
    }

    /**
     * @param Account $account
     * @return FormInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createForm(Account $account): FormInterface
    {
        $form = $this->container->get('form.factory')->create(AccountType::class, $account);
        // 新規の場合
        $passwordOption = [
            "label" => "パスワード",
            "mapped" => false,
            "attr" => [
                "placeholder" => "半角英数字記号で6文字以上"
            ],
        ];
        if(!$account->getId()) {
            $passwordOption["constraints"] = [
                new NotBlank([
                    "message" => "未入力です"
                ]),
            ];
        } else {
            $passwordOption["required"] = false;
            $passwordOption["help"] = "パスワード変更時に入力してください";
            $passwordOption["constraints"] = [];
        }
        $passwordOption["constraints"][] = new Length([
            "min" => 6,
            "minMessage" => "6文字以上で設定してください"
        ]);
        $form->add('plain_password', PasswordType::class, $passwordOption);

        if($account->hasSuperAdmin()) {
            $form->get('role_super_admin')->setData(true);
        }

        return $form;
    }

    /**
     * @param Account|UserInterface $account
     * @param FormInterface $form
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistEntity(Account|UserInterface $account, FormInterface $form): int
    {
        $isCreate = !$account->getId();
        if($isCreate || $form->get('plain_password')->getData()) {
            $account
                ->setPassword(
                    $this->passwordHasher->hashPassword($account, $form->get('plain_password')->getData())
                );
        }
        if($form->has('role_super_admin')) {
            $roles = ["ROLE_ADMIN"];
            if($form->get('role_super_admin')->getData()) {
                $roles[] = "ROLE_SUPER_ADMIN";
            }
            $account->setRoles($roles);
        }
        $this->repository->add($account);

        $this->persistLog(
            "account",
            $isCreate,
            $account->getId(),
            $account->getName(),
        );

        return $isCreate ? self::CREATED : self::UPDATED;
    }

    /**
     * @param Account $account
     * @return int
     * @throws CannotExecuteException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteEntity(Account $account): int
    {
        $id = $account->getId();
        if($id === 1) {
            throw new CannotExecuteException(
                "このアカウントは削除できません"
            );
        }
        if($account === $this->security->getUser()) {
            throw new CannotExecuteException(
                "ログインユーザーを削除しようとしています"
            );
        }

        $this->repository->remove($account);

        $this->deleteLog("account", $id, $account->getName());


        return self::DELETED;
    }
}