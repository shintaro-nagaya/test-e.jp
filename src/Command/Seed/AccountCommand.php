<?php

namespace App\Command\Seed;

use App\Entity\Account\Account;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed:account',
    description: 'ログインアカウントのシード',
)]
class AccountCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $AccountRepository = $this->entityManager->getRepository(Account::class);
        $Account = $AccountRepository->findOneBy(["id" => 1]);
        if(!$Account) {
            $io->title('管理画面アカウント作成');
            $q = new Question("ログイン Email? : ", false);
            $email = $helper->ask($input, $output, $q);
            if(!$email) {
                $io->caution('キャンセルされました');
                return Command::SUCCESS;
            }
            $q = new Question("ログイン パスワード? : ", false);
            $password = $helper->ask($input, $output, $q);
            if(!$password) {
                $io->caution('キャンセルされました');
                return Command::SUCCESS;
            }
            $Account = (new Account())
                ->setEmail($email)
                ->setRoles(["ROLE_ADMIN", "ROLE_SUPER_ADMIN"])
                ->setName('TripleE メンテナンス')
                ->setAdminLightMode(false)
                ;
            $Account->setPassword($this->passwordHasher->hashPassword($Account, $password));
            $this->entityManager->persist($Account);
            $this->entityManager->flush();
            $io->success("管理画面アカウントを作成しました");
            return Command::SUCCESS;
        } else {
            $q = new ConfirmationQuestion("アカウントは作成されています。メールアドレス・パスワードを変更しますか? [n] : ", "n", "/^y/i");
            if(!$helper->ask($input, $output, $q)) {
                $io->success('キャンセルしました');
                return Command::SUCCESS;
            }

            $doUpdate = false;
            $q = new Question("メールアドレス? [未入力で変更無し] : ", false);
            $email = $helper->ask($input, $output, $q);
            if($email) {
                $Account->setEmail($email);
                $doUpdate = true;
            }

            $q = new Question("パスワード? [未入力で変更無し] : ", false);
            $password = $helper->ask($input, $output, $q);
            if($password) {
                $Account->setPassword($this->passwordHasher->hashPassword($Account, $password));
                $doUpdate = true;
            }
            if(true === $doUpdate) {
                $this->entityManager->persist($Account);
                $this->entityManager->flush();
                $io->success('変更しました');
            } else {
                $io->note('変更はありませんでした');
            }
        }

        return Command::SUCCESS;
    }
}
