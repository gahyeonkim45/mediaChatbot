#-*- coding: utf-8 -*-
from math import log
import operator
#from konlpy.tag import Mecab
from konlpy.tag import Mecab
import matplotlib.pyplot as plt
import copy
import sys
import dataSet as Ds

decisionNode = dict(boxstyle="sawtooth", fc="0.8")
leafNode = dict(boxstyle="round4", fc="0.8")
arrow_args = dict(arrowstyle="<-")

def getNumLeafs(myTree):
    numLeafs = 0
    
    tempkeys = myTree.keys()
    templist = list(tempkeys)
    
    firstStr = templist[0]
    secondDict = myTree[firstStr]
    for key in secondDict.keys():
        if type(secondDict[key]).__name__=='dict':#test to see if the nodes are dictonaires, if not they are leaf nodes
            numLeafs += getNumLeafs(secondDict[key])
        else:   numLeafs +=1
    return numLeafs

def getTreeDepth(myTree):
    maxDepth = 0
    
    tempkeys = myTree.keys()
    templist = list(tempkeys)
    
    firstStr = templist[0]
    
    secondDict = myTree[firstStr]
    for key in secondDict.keys():
        if type(secondDict[key]).__name__=='dict':#test to see if the nodes are dictonaires, if not they are leaf nodes
            thisDepth = 1 + getTreeDepth(secondDict[key])
        else:   thisDepth = 1
        if thisDepth > maxDepth: maxDepth = thisDepth
    return maxDepth

def plotNode(nodeTxt, centerPt, parentPt, nodeType):
    createPlot.ax1.annotate(nodeTxt, xy=parentPt,  xycoords='axes fraction',
             xytext=centerPt, textcoords='axes fraction',
             va="center", ha="center", bbox=nodeType, arrowprops=arrow_args )

def plotMidText(cntrPt, parentPt, txtString):
    xMid = (parentPt[0]-cntrPt[0])/2.0 + cntrPt[0]
    yMid = (parentPt[1]-cntrPt[1])/2.0 + cntrPt[1]
    createPlot.ax1.text(xMid, yMid, txtString, va="center", ha="center", rotation=30)

def plotTree(myTree, parentPt, nodeTxt):#if the first key tells you what feat was split on
    numLeafs = getNumLeafs(myTree)  #this determines the x width of this tree
    depth = getTreeDepth(myTree)

    tempkeys = myTree.keys()
    templist = list(tempkeys)
    
    firstStr = templist[0]
    
   #the text label for this node should be this
    cntrPt = (plotTree.xOff + (1.0 + float(numLeafs))/2.0/plotTree.totalW, plotTree.yOff)
    plotMidText(cntrPt, parentPt, nodeTxt)
    plotNode(firstStr, cntrPt, parentPt, decisionNode)
    secondDict = myTree[firstStr]
    plotTree.yOff = plotTree.yOff - 1.0/plotTree.totalD
    for key in secondDict.keys():
        if type(secondDict[key]).__name__=='dict':#test to see if the nodes are dictonaires, if not they are leaf nodes   
            plotTree(secondDict[key],cntrPt,str(key))        #recursion
        else:   #it's a leaf node print the leaf node
            plotTree.xOff = plotTree.xOff + 1.0/plotTree.totalW
            plotNode(secondDict[key], (plotTree.xOff, plotTree.yOff), cntrPt, leafNode)
            plotMidText((plotTree.xOff, plotTree.yOff), cntrPt, str(key))
    plotTree.yOff = plotTree.yOff + 1.0/plotTree.totalD
#if you do get a dictonary you know it's a tree, and the first element will be another dict

def createPlot(inTree):
    fig = plt.figure(1, facecolor='white')
    fig.clf()
    axprops = dict(xticks=[], yticks=[])
    createPlot.ax1 = plt.subplot(111, frameon=False, **axprops)    #no ticks
    #createPlot.ax1 = plt.subplot(111, frameon=False) #ticks for demo puropses 
    plotTree.totalW = float(getNumLeafs(inTree))
    plotTree.totalD = float(getTreeDepth(inTree))
    plotTree.xOff = -0.5/plotTree.totalW; plotTree.yOff = 1.0;
    plotTree(inTree, (0.5,1.0), '')
    plt.show()

def calcShannonEnt(dataSet):
    numEntries = len(dataSet)
    labelCounts = {}
    for featVec in dataSet:
        currentLabel = featVec[-1]
        if (currentLabel not in labelCounts.keys()):
            labelCounts[currentLabel] = 0
        labelCounts[currentLabel] += 1
    shannonEnt = 0.0
    for key in labelCounts:
        prob = float(labelCounts[key])/numEntries;
        shannonEnt +=  -prob * log(prob, 2)
    return shannonEnt


def createDataSet(testStrlist):
    # dataSet = [
    #            ['no','no','no','no','no','yes','yes','no','no','평균 학점'],
    #            ['no','yes','no','yes','no','yes','yes','no','no','전공 선택'],
    #            ['no','yes','yes','no','no','no','no','no','no','전공 필수']
    #             # ['no','no','no','yes','brochure'],
    #             # ['yes','yes','no','no','toeic score'],
    #             # ['yes','no','yes','no','toeic graduate condition'],
    #             # ['yes','yes','yes','no','toeic graduate score']
    #            ]

    #dataSet = []
    labels = ['성적','등수','교수','언제','일정','공인','졸업','필수','선택','학점','전공','학년','선수']
    idx = -1

    for value in testStrlist:
     for index, item in enumerate(labels):
        if value == item:
            idx = index
    
    if(idx == 4) : #언제 = 일정
        idx = idx-1
    elif(idx == 1) : # 성적 = 등수
        idx = 0
    elif(idx >= 6) :
        idx = 4

    dataSet, labels = Ds.getDataSet(idx)

    return dataSet, labels


def splitDataSet(dataSet, axis, value):
    retDataSet = []
    for featVec in dataSet:
        if featVec[axis] == value:
            reducedFeatVec = featVec[:axis]
            reducedFeatVec.extend(featVec[axis+1:])
            retDataSet.append(reducedFeatVec)
    return retDataSet

def chooseBestFeatureToSplit(dataSet):
    numFeatures = len(dataSet[0]) - 1
    baseEntropy = calcShannonEnt(dataSet)
    # 분할하기 전의 엔트로피값 구함
  
    bestInfoGain = 0.0
    bestFeature = -1
    for i in range(numFeatures):
        featList = [example[i] for example in dataSet]
        uniqueVals = set(featList)
        newEntropy = 0.0
        for value in uniqueVals:
            subDataSet = splitDataSet(dataSet, i, value)
            prob = len(subDataSet)/float(len(dataSet))
            newEntropy = prob * calcShannonEnt(subDataSet)
        infoGain = baseEntropy - newEntropy
        if infoGain > bestInfoGain:
            bestInfoGain = infoGain
            bestFeature = i
    return bestFeature

def majorityCnt(classList):
    classCount = {}
    for vote in classList:
        if vote not in classCount: classCount[vote] = 0
        classCount[vote] += 1
    sortedClassCount = sorted(classCount.iteritems(), key=operator.itemgetter(1), reverse=True)
    return sortedClassCount[0][0]

def createTree(dataSet, labels):
    classList = [example[-1] for example in dataSet]
      # dataSet에서 가장 마지막 항목들만 추출해서 리스트를 생성
      
    if classList.count(classList[0]) == len(classList):
        return classList[0]
    
      # 첫번째 항목의 개수 = 전체 리스트의 개수
      # ex. ['yes','yes','yes'] = yes return 

    if len(dataSet[0]) == 1:
        return majorityCnt(classList)
      #dataSet이 1개 = 더 이상 분류할 속성이 존재하지 않을 때,
    
    #dataSet 값이 1개 이상, 동일한 속성 존재하지 않으면 
    bestFeat = chooseBestFeatureToSplit(dataSet)
    #chooseBestFeatureToSplit 이용해 information gain이 큰 값을 찾음
  
    bestFeatLabel = labels[bestFeat]
    myTree = {bestFeatLabel: {}}
    del(labels[bestFeat])
    featValues = [example[bestFeat] for example in dataSet]
    uniqueVals = set(featValues)
    for value in uniqueVals:
        subLabels = labels[:]
        myTree[bestFeatLabel][value] = createTree(splitDataSet(dataSet, bestFeat, value), subLabels)
    return myTree


def classify(inputTree,featLabels,testVec):

    tempkeys = inputTree.keys()
    templist = list(tempkeys)
    
    firstStr = templist[0]

    secondDict = inputTree[firstStr]

    
    featIndex = featLabels.index(firstStr)
    
    key = testVec[featIndex]
    
    valueOfFeat = secondDict[key]
    if isinstance(valueOfFeat, dict): 
        classLabel = classify(valueOfFeat, featLabels, testVec)
    else: classLabel = valueOfFeat
    return classLabel

def changeStrlist(labels, testStrlist) :

    testVec = copy.deepcopy(labels)

    for value in testStrlist:
        for index, item in enumerate(testVec):
            if value == item:
                testVec[index] = "1"

    for index, item in enumerate(testVec):
        if testVec[index] != "1":
            testVec[index] = "0"

   # print(testVec)
    
    return testVec

    
if __name__ == '__main__':
            

    #kkma = Kkma()
    mecab = Mecab()
    sentence = sys.argv[1]
    testStrlist = mecab.morphs(sentence)
    # 형태소 나누기
    
    #print(testStrlist)

#tree
    dataSet, labels = createDataSet(testStrlist)
    #print(dataSet)
    #print(labels)

    templabels = copy.deepcopy(labels)
    myTree = createTree(dataSet, labels)
#

    testVec = changeStrlist(templabels, testStrlist)

    #print(templabels, testVec)
    
    resultStr = classify(myTree,templabels,testVec)
    
    if resultStr == '60':
        semester = 0
        year = 0

        list = copy.deepcopy(testStrlist)

        for index, item in enumerate(list):
            if item == '학년' :
                year = list[(index-1)]
            elif item == '학기' :
                semester = list[(index-1)]


        if semester != '0' and year == '0' :
            if semester == '1' :
                resultStr = '60'
            elif semester == '2' :
                resultStr = '61'
        elif semester == '0' and year != '0' :
            if year == '1' :
                resultStr = '62'
            elif year == '2' :
                resultStr = '63'
            elif year == '3' :
                resultStr = '64'
            elif year == '4' :
                resultStr = '65'
        else :
            if year == '1' and semester == '1' :
                resultStr = '66'
            elif year == '1' and semester == '2' :
                resultStr = '67'
            elif year == '2' and semester == '1' :
                resultStr = '68'
            elif year == '2' and semester == '2' :
                resultStr = '69'
            elif year == '3' and semester == '1' :
                resultStr = '70'
            elif year == '3' and semester == '2' :
                resultStr = '71'
            elif year == '4' and semester == '1' :
                resultStr = '72'
            elif year == '4' and semester == '2' :
                resultStr = '73'

    elif resultStr == '95' : # 월 일정

        month = 0
        list = copy.deepcopy(testStrlist)

        for index, item in enumerate(list):
            if item == '월' :
                month = list[(index-1)]

        data = int(resultStr) + int(month)-1

        resultStr = str(data)

    elif resultStr == '107' :

        semester = 0
        list = copy.deepcopy(testStrlist)

        for index, item in enumerate(list):
            if item == '학기' :
                semester = list[(index-1)]

        if semester == '2' :
            resultStr = '108'

    elif resultStr == '109' :
        month = 0
        list = copy.deepcopy(testStrlist)

        for index, item in enumerate(list):
            if item == '월' :
                month = list[(index-1)]

        data = int(resultStr) + int(month)

        resultStr = str(data)
        
    elif resultStr == '121' :
        semester = 0
        list = copy.deepcopy(testStrlist)

        for index, item in enumerate(list):
            if item == '학기' :
                semester = list[(index-1)]

        if semester == '2' :
            resultStr = '122'

    print(resultStr)

    #createPlot(myTree)

