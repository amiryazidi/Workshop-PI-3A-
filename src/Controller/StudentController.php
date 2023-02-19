<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }
    #[Route('/student/add', name: 'student_add')]
    public function add(ManagerRegistry $doctrine, Request $req): Response
    {
        $em=$doctrine->getManager();
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($student);
            $em->flush();
            return $this->redirectToRoute('student_add');
        }
        return $this->render('student/add.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
    #[Route('/allStudents', name: 'list')]
    public function getStudents(StudentRepository $repo,SerializerInterface $serializer, NormalizerInterface $normalizer)
    {
        $students=$repo->findAll();
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
        //* students en  tableau associatif simple.
        // $studentsNormalises = $normalizer->normalize($students, 'json',['groups'=>"students"]);

        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($studentsNormalises);

        $json = $serializer->serialize($students, 'json',['groups'=>"students"]);

        //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
        return new Response($json);
    }
    #[Route("/Student/{id}", name: "student")]
    public function StudentId($id, NormalizerInterface $normalizer, StudentRepository $repo)
    {
        $student = $repo->find($id);
        $studentNormalises = $normalizer->normalize($student, 'json', ['groups' => "students"]);
        return new Response(json_encode($studentNormalises));
    }


    #[Route("addStudentJSON/new", name: "addStudentJSON")]
    public function addStudentJSON(Request $req,  SerializerInterface $serializer, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $student = new Student();
        $student->setNsc($req->get('nsc'));
        $student->setEmail($req->get('email'));
        $em->persist($student);
        $em->flush();

        $jsonContent = $Normalizer->normalize($student, 'json', ['groups' => 'students']);
        return new Response(json_encode($jsonContent));
        return new Response(json_encode($jsonContent));

    }

    #[Route("updateStudentJSON/{id}", name: "updateStudentJSON")]
    public function updateStudentJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository(Student::class)->find($id);
        $student->setNsc($req->get('nsc'));
        $student->setEmail($req->get('email'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($student, 'json', ['groups' => 'students']);
        return new Response("Student updated successfully " . json_encode($jsonContent));
    }

    #[Route("deleteStudentJSON/{id}", name: "deleteStudentJSON")]
    public function deleteStudentJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository(Student::class)->find($id);
        $em->remove($student);
        $em->flush();
        $jsonContent = $Normalizer->normalize($student, 'json', ['groups' => 'students']);
        return new Response("Student deleted successfully " . json_encode($jsonContent));
    }
}